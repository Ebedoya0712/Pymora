<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\StockTransfer;
use App\Models\Branch;
use Illuminate\Http\Request;
use Exception;

class TransferController extends Controller
{
    public function index()
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        if (!$tenant) {
            $tenant = (object) ['id' => 1, 'name' => 'Bodega & Abasto El Sol C.A.'];
            $transfers = collect([
                (object) [
                    'transfer_number' => 'TRF-2026-001',
                    'status' => 'in_transit',
                    'created_at' => now()->subHours(5),
                    'fromBranch' => (object) ['name' => 'Almacén Central Las Mercedes'],
                    'toBranch' => (object) ['name' => 'Sucursal Principal Altamira']
                ],
                (object) [
                    'transfer_number' => 'TRF-2026-002',
                    'status' => 'received',
                    'created_at' => now()->subDays(2),
                    'fromBranch' => (object) ['name' => 'Sucursal Principal Altamira'],
                    'toBranch' => (object) ['name' => 'Almacén Central Las Mercedes']
                ]
            ]);
            $branches = collect();
        } else {
            $transfers = StockTransfer::where('tenant_id', $tenant->id)->with(['fromBranch', 'toBranch'])->latest()->get();
            $branches = Branch::where('tenant_id', $tenant->id)->get();
        }

        return view('transfers.index', compact('tenant', 'transfers', 'branches'));
    }
}
