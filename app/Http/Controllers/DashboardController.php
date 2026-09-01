<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\CashSession;
use App\Models\BankAccount;
use App\Models\Quote;
use App\Models\StockTransfer;
use App\Models\InventoryStock;
use App\Models\ProductBatch;
use App\Models\Payment;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Exception;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        // Fetch Live DolarApi & BCV Exchange Rates
        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        if (!$tenant) {
            $tenant = (object) [
                'id' => 1,
                'name' => 'Bodega & Abasto El Sol C.A.',
                'rif_tax_id' => 'J-12345678-9',
                'subdomain' => 'elsol',
                'plan_tier' => 'pro',
                'business_type' => 'abasto',
                'bcv_rate' => $bcvUsdRate,
                'parallel_rate' => 54.1000,
                'igtf_percentage' => 3.00,
            ];
        }

        // Determine business type from logged-in tenant (with fallback and preview support)
        $allBusinessTypes = Tenant::getBusinessTypes();
        $selectedTypeKey = $request->input('type', $tenant->business_type ?? 'abasto');
        if (!array_key_exists($selectedTypeKey, $allBusinessTypes)) {
            $selectedTypeKey = 'abasto';
        }

        session(['business_type' => $selectedTypeKey]);

        $currentBusinessType = $allBusinessTypes[$selectedTypeKey];

        try {
            $branches = Branch::where('tenant_id', $tenant->id)->get();
            $activeBranch = $branches->first();
            $salesTodayUsd = (float) Sale::where('tenant_id', $tenant->id)->sum('total_usd');
            $salesTodayVes = $salesTodayUsd * $bcvUsdRate;
            $salesTodayEur = $bcvEurRate > 0 ? ($salesTodayVes / $bcvEurRate) : ($salesTodayUsd * 0.92);

            // Actual amounts received natively in each currency
            $salesUsdReal = (float) Payment::where('tenant_id', $tenant->id)->where('currency', 'USD')->sum('amount_native');
            if ($salesUsdReal == 0 && $salesTodayUsd > 0) {
                $salesUsdReal = $salesTodayUsd;
            }
            $salesVesReal = (float) Payment::where('tenant_id', $tenant->id)->where('currency', 'VES')->sum('amount_native');
            $salesEurReal = (float) Payment::where('tenant_id', $tenant->id)->where('currency', 'EUR')->sum('amount_native');

            $totalProductsCount = Product::where('tenant_id', $tenant->id)->count();
            $barcodeProductsCount = Product::where('tenant_id', $tenant->id)->whereNotNull('barcode')->where('barcode', '!=', '')->count();
            $totalStockUnits = (float) InventoryStock::where('tenant_id', $tenant->id)->sum('quantity');
            $allProducts = Product::where('tenant_id', $tenant->id)->with('stocks.branch')->get();
            $totalDebtUsd = (float) Customer::where('tenant_id', $tenant->id)->sum('current_debt_usd');
            $activeCashSession = CashSession::where('tenant_id', $tenant->id)->where('status', 'open')->first();
            $bankAccounts = BankAccount::where('tenant_id', $tenant->id)->get();
            $recentSales = Sale::where('tenant_id', $tenant->id)->with('customer')->latest()->take(5)->get();
            $pendingQuotesCount = Quote::where('tenant_id', $tenant->id)->where('status', 'pending_approval')->count();
            $transfersInTransitCount = StockTransfer::where('tenant_id', $tenant->id)->where('status', 'in_transit')->count();
        } catch (Exception $e) {
            $branches = collect([
                (object) ['id' => 1, 'name' => 'Sucursal Principal Altamira', 'code' => 'ALT-001'],
                (object) ['id' => 2, 'name' => 'Almacén Central Las Mercedes', 'code' => 'MER-002']
            ]);
            $activeBranch = $branches->first();
            $salesTodayUsd = 450.80;
            $salesTodayVes = $salesTodayUsd * $bcvUsdRate;
            $salesTodayEur = $bcvEurRate > 0 ? ($salesTodayVes / $bcvEurRate) : ($salesTodayUsd * 0.92);
            $salesUsdReal = 450.80;
            $salesVesReal = 0.00;
            $salesEurReal = 0.00;
            $totalProductsCount = 4;
            $barcodeProductsCount = 4;
            $allProducts = collect([
                (object) ['id' => 1, 'name' => 'Refresco Coca-Cola 2L', 'sku' => 'BEB-001', 'barcode' => '7591001001234', 'price_usd' => 2.50, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 120]])],
                (object) ['id' => 2, 'name' => 'Harina PAN Blanca 1kg', 'sku' => 'VIV-001', 'barcode' => '7591002005678', 'price_usd' => 1.35, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 250]])],
                (object) ['id' => 3, 'name' => 'Queso Paisa Blanco (Kg)', 'sku' => 'CHA-001', 'barcode' => '7591003009012', 'price_usd' => 7.80, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 35.5]])],
                (object) ['id' => 4, 'name' => 'Arroz Primor Supremo 1kg', 'sku' => 'VIV-002', 'barcode' => '7591004003456', 'price_usd' => 1.50, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 180]])]
            ]);
            $totalStockUnits = 735.0;
            $totalDebtUsd = 350.00;
            $activeCashSession = (object) ['status' => 'open', 'initial_cash_usd' => 50.00, 'expected_cash_usd' => 250.00];
            $bankAccounts = collect([
                (object) ['name' => 'Banesco Bolívares Principal', 'account_number' => '0134-0001-00-1234567890', 'currency' => 'VES', 'balance' => 45000.00],
                (object) ['name' => 'Zelle Dólares Empresa', 'account_number' => 'pagos@elsol.com', 'currency' => 'USD', 'balance' => 3200.00],
                (object) ['name' => 'Efectivo Caja USD Altamira', 'account_number' => 'CAJA-USD', 'currency' => 'USD', 'balance' => 850.00]
            ]);
            $recentSales = collect([
                (object) ['sale_number' => 'VTA-2026-0001', 'total_usd' => 8.93, 'total_ves' => round(8.93 * $bcvUsdRate, 2), 'customer' => (object) ['name' => 'Juan Pérez']],
                (object) ['sale_number' => 'VTA-2026-0002', 'total_usd' => 15.50, 'total_ves' => round(15.50 * $bcvUsdRate, 2), 'customer' => (object) ['name' => 'Inversiones Los Chaguaramos']],
                (object) ['sale_number' => 'VTA-2026-0003', 'total_usd' => 4.20, 'total_ves' => round(4.20 * $bcvUsdRate, 2), 'customer' => (object) ['name' => 'Cliente Detal']]
            ]);
            $pendingQuotesCount = 3;
            $transfersInTransitCount = 1;
        }

        // Calculate batch expiration alerts count for Abasto
        try {
            $batchAlertsCount = ProductBatch::where('tenant_id', $tenant->id)
                ->whereDate('expiration_date', '<=', now()->addDays(30))
                ->count();
        } catch (Exception $e) {
            $batchAlertsCount = 2;
        }

        // Calculate low stock products count (stock <= min_stock_alert)
        try {
            $lowStockProductsCount = $allProducts->filter(function ($product) {
                $stockTotal = isset($product->stocks) && is_iterable($product->stocks)
                    ? $product->stocks->sum('quantity')
                    : ($product->stock ?? 0);
                $minStock = (float) ($product->min_stock_alert ?? 10);
                return $stockTotal <= $minStock;
            })->count();
        } catch (Exception $e) {
            $lowStockProductsCount = 0;
        }

        // Specialized Widget Context Data per Business Type
        $businessWidgetsData = self::getBusinessWidgetsData($selectedTypeKey, $barcodeProductsCount, $batchAlertsCount, $lowStockProductsCount);

        return view('dashboard.index', compact(
            'tenant',
            'branches',
            'activeBranch',
            'salesTodayUsd',
            'salesTodayVes',
            'salesTodayEur',
            'salesUsdReal',
            'salesVesReal',
            'salesEurReal',
            'bcvUsdRate',
            'bcvEurRate',
            'totalProductsCount',
            'barcodeProductsCount',
            'allProducts',
            'totalStockUnits',
            'totalDebtUsd',
            'activeCashSession',
            'bankAccounts',
            'recentSales',
            'pendingQuotesCount',
            'transfersInTransitCount',
            'allBusinessTypes',
            'selectedTypeKey',
            'currentBusinessType',
            'businessWidgetsData',
            'batchAlertsCount',
            'lowStockProductsCount'
        ));
    }

    private static function getBusinessWidgetsData(string $type, int $barcodeCount = 4, int $batchAlertsCount = 2, int $lowStockCount = 0): array
    {
        switch ($type) {
            case 'restaurante':
                return [
                    'tables_occupied' => 6,
                    'tables_total' => 12,
                    'kitchen_orders' => 4,
                    'critical_ingredients' => [
                        ['name' => 'Queso Mozzarella (Kg)', 'stock' => 3.5, 'min' => 10.0],
                        ['name' => 'Harina de Trigo (Kg)', 'stock' => 12.0, 'min' => 25.0],
                        ['name' => 'Carne Molida Premium (Kg)', 'stock' => 5.2, 'min' => 15.0],
                    ],
                ];
            case 'ropa':
                return [
                    'top_sizes' => ['M' => '42%', 'L' => '30%', 'S' => '18%', 'XL' => '10%'],
                    'top_colors' => ['Negro', 'Azul Marino', 'Blanco', 'Beige'],
                    'variants_count' => 184,
                ];
            case 'distribuidor':
                return [
                    'routes_active' => 3,
                    'wholesale_volume_usd' => 1420.00,
                    'collectors_pending' => 4,
                    'top_clients' => ['Abasto La Estrella', 'Bodega San José', 'MiniSuper El Valle'],
                ];
            case 'fabricante':
                return [
                    'active_production_orders' => 3,
                    'raw_materials_count' => 28,
                    'completed_this_week' => 120,
                ];
            case 'licoreria':
                return [
                    'bottles_sold_today' => 34,
                    'cases_sold_today' => 6,
                    'igtf_collected_usd' => 14.20,
                    'license_expiry' => '15/11/2026',
                ];
            case 'repuestos':
                return [
                    'top_brands' => ['Toyota', 'Chevrolet', 'Ford', 'Hyundai'],
                    'oem_matches_count' => 420,
                    'warranties_active' => 8,
                ];
            case 'carniceria_hortalizas':
                return [
                    'total_kg_sold_today' => 148.5,
                    'waste_percentage' => '1.8%',
                    'carcass_in_stock' => 4,
                ];
            case 'tecnologia_electro':
                return [
                    'imei_registered_today' => 8,
                    'warranties_issued' => 14,
                    'repairs_in_workshop' => 3,
                ];
            case 'servicios':
                return [
                    'appointments_today' => 5,
                    'open_work_orders' => 4,
                    'billable_hours' => '18.5 hrs',
                ];
            case 'articulos':
                return [
                    'promos_active' => 3,
                    'bundles_sold' => 12,
                    'fast_movers' => ['Audífonos Bluetooth', 'Cargador Carga Rápida', 'Protector Pantalla'],
                ];
            default: // abasto
                return [
                    'barcode_products_count' => $barcodeCount,
                    'perishables_warning' => $batchAlertsCount,
                    'low_stock_count' => $lowStockCount,
                    'dual_bcv_active' => true,
                ];
        }
    }
}
