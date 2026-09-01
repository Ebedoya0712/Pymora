<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CashSession;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\InventoryStock;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        $tenantId = $tenant ? $tenant->id : 1;

        if (!$tenant) {
            $tenant = (object) [
                'id' => 1, 
                'name' => 'Bodega & Abasto El Sol C.A.', 
                'bcv_rate' => $bcvUsdRate
            ];
        } else {
            $tenant->bcv_rate = $bcvUsdRate;
        }

        $categories = Category::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $customers = Customer::where('tenant_id', $tenantId)->get();
        $activeSession = CashSession::where('tenant_id', $tenantId)->where('status', 'open')->first() ?? (object) ['status' => 'open'];

        return view('pos.index', compact(
            'tenant', 
            'categories', 
            'products', 
            'customers', 
            'activeSession', 
            'bcvUsdRate', 
            'bcvEurRate'
        ));
    }

    public function store(Request $request)
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        $tenantId = $tenant ? $tenant->id : 1;
        $tenantName = $tenant ? $tenant->name : 'Bodega & Abasto El Sol C.A.';

        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        $totalUsd = max(0, (float) $request->input('total_usd', 0));
        $customerId = $request->input('customer_id');
        $paymentCurrency = $request->input('currency', 'USD');
        $paymentMethod = $request->input('payment_method', 'cash_usd');
        $amountReceivedNative = (float) $request->input('amount_received_native', 0);
        $changeDueVes = (float) $request->input('change_due_ves', 0);
        $changeDueUsd = (float) $request->input('change_due_usd', 0);
        $referenceCode = $request->input('reference_code', 'POS-DIRECT');
        $itemsJson = $request->input('items_json', '[]');
        $items = json_decode($itemsJson, true) ?? [];

        $totalVes = round($totalUsd * $bcvUsdRate, 2);

        DB::beginTransaction();
        try {
            $saleNumber = 'VTA-' . date('Ymd') . '-' . str_pad((string)rand(1, 99999), 5, '0', STR_PAD_LEFT);
            
            $sale = Sale::create([
                'tenant_id' => $tenantId,
                'branch_id' => 1,
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $customerId ?: null,
                'sale_number' => $saleNumber,
                'subtotal_usd' => round($totalUsd / 1.19, 2),
                'tax_usd' => round(($totalUsd / 1.19) * 0.16, 2),
                'igtf_usd' => $paymentCurrency !== 'VES' ? round(($totalUsd / 1.19) * 0.03, 2) : 0,
                'total_usd' => $totalUsd,
                'total_ves' => $totalVes,
                'exchange_rate_bcv' => $bcvUsdRate,
                'status' => 'completed',
                'payment_status' => 'paid',
                'notes' => 'Cobro Multimoneda (' . strtoupper($paymentCurrency) . ') vía ' . $paymentMethod,
            ]);

            // Create Sale Items & Deduct Stock
            foreach ($items as $item) {
                $itemPrice = (float) ($item['price'] ?? 0);
                $itemQty = (float) ($item['qty'] ?? 1);
                $itemSubtotal = round($itemPrice * $itemQty, 2);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'] ?? null,
                    'product_name' => $item['name'] ?? 'Producto POS',
                    'quantity' => $itemQty,
                    'unit_price_usd' => $itemPrice,
                    'tax_rate' => 16.00,
                    'subtotal_usd' => $itemSubtotal,
                ]);

                // Deduct stock if product ID is present
                if (!empty($item['id'])) {
                    $stock = InventoryStock::where('tenant_id', $tenantId)
                        ->where('product_id', $item['id'])
                        ->first();
                    if ($stock && $stock->quantity >= $itemQty) {
                        $stock->decrement('quantity', $itemQty);
                    }
                }
            }

            // Create Payment record
            Payment::create([
                'tenant_id' => $tenantId,
                'sale_id' => $sale->id,
                'payment_method' => $paymentMethod,
                'currency' => $paymentCurrency,
                'amount_usd' => $totalUsd,
                'amount_native' => $amountReceivedNative > 0 ? $amountReceivedNative : ($paymentCurrency === 'VES' ? $totalVes : ($paymentCurrency === 'EUR' ? round($totalVes / $bcvEurRate, 2) : $totalUsd)),
                'exchange_rate' => $paymentCurrency === 'EUR' ? $bcvEurRate : $bcvUsdRate,
                'reference_code' => $referenceCode,
            ]);

            DB::commit();

            $currencySymbol = $paymentCurrency === 'EUR' ? '€' : ($paymentCurrency === 'VES' ? 'Bs' : '$');
            $successMessage = "¡Venta {$saleNumber} procesada exitosamente en {$tenantName}! Total: {$totalUsd} USD ($ {$totalVes} VES). Moneda de pago: {$paymentCurrency}.";
            if ($changeDueVes > 0) {
                $successMessage .= " Cambio/Vuelto entregado: Bs " . number_format($changeDueVes, 2, ',', '.') . " VES ($" . number_format($changeDueUsd, 2) . " USD).";
            }

            return redirect()->route('pos.index')->with('success', $successMessage);

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('pos.index')->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }
}
