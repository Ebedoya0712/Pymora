<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Exception;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        if (!$tenant) {
            $tenant = (object) [
                'id' => 1,
                'name' => 'Bodega & Abasto El Sol C.A.',
                'business_type' => 'abasto'
            ];
        }

        // Live Exchange Rates
        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);

        $filter = $request->input('filter', 'all');
        $search = trim((string) $request->input('search', ''));
        $branchId = $request->input('branch_id', 'all');

        $query = ProductBatch::where('tenant_id', $tenant->id)
            ->with(['product.category', 'branch'])
            ->orderBy('expiration_date', 'asc');

        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                  });
            });
        }

        if ($filter === 'expired') {
            $query->whereDate('expiration_date', '<', now());
        } elseif ($filter === 'expiring_soon') {
            $query->whereDate('expiration_date', '>=', now())
                  ->whereDate('expiration_date', '<=', now()->addDays(30));
        } elseif ($filter === 'valid') {
            $query->whereDate('expiration_date', '>', now()->addDays(30));
        }

        $batches = $query->get();

        // High Level Metrics
        $allBatches = ProductBatch::where('tenant_id', $tenant->id)->get();
        $totalBatchesCount = $allBatches->count();
        $alertBatchesCount = $allBatches->filter(fn($b) => $b->days_until_expiration >= 0 && $b->days_until_expiration <= 30)->count();
        $expiredBatchesCount = $allBatches->filter(fn($b) => $b->days_until_expiration < 0)->count();
        $totalStockInBatches = $allBatches->sum('quantity');

        $products = Product::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $branches = Branch::where('tenant_id', $tenant->id)->get();

        return view('batches.index', compact(
            'tenant',
            'batches',
            'products',
            'branches',
            'filter',
            'search',
            'branchId',
            'totalBatchesCount',
            'alertBatchesCount',
            'expiredBatchesCount',
            'totalStockInBatches',
            'bcvUsdRate'
        ));
    }

    public function store(Request $request)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_number' => 'required|string|max:100',
            'quantity' => 'required|numeric|min:0.01',
            'expiration_date' => 'required|date',
            'manufactured_date' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['status'] = 'active';

        ProductBatch::create($validated);

        return redirect()->route('batches.index')->with('success', '¡Lote de producto registrado exitosamente con fecha de vencimiento!');
    }

    public function destroy($id)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];
        $batch = ProductBatch::where('tenant_id', $tenant->id)->findOrFail($id);
        $batch->delete();

        return redirect()->route('batches.index')->with('success', 'Lote eliminado del sistema.');
    }
}
