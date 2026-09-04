<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Branch;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
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
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        $filter = $request->input('filter', 'all');
        $categoryId = $request->input('category_id', 'all');
        $search = trim((string) $request->input('search', ''));

        $query = Product::where('tenant_id', $tenant->id)
            ->with(['category', 'stocks.branch'])
            ->orderBy('name', 'asc');

        if ($categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $allProducts = $query->get();

        // Calculate total stock and low stock flag for each product
        $allProducts->each(function ($product) {
            $product->total_stock = (float) $product->stocks->sum('quantity');
            $product->min_alert = (float) ($product->min_stock_alert ?? 10);
            $product->is_low_stock = $product->total_stock <= $product->min_alert;
        });

        // Filter products based on tab
        if ($filter === 'low_stock') {
            $products = $allProducts->filter(fn($p) => $p->is_low_stock)->values();
        } elseif ($filter === 'normal') {
            $products = $allProducts->filter(fn($p) => !$p->is_low_stock)->values();
        } else {
            $products = $allProducts;
        }

        // All low stock products for modal alert
        $lowStockProducts = $allProducts->filter(fn($p) => $p->is_low_stock)->values();
        $lowStockCount = $lowStockProducts->count();
        $totalProductsCount = $allProducts->count();
        $totalStockUnits = $allProducts->sum('total_stock');
        $totalInventoryValueUsd = $allProducts->sum(fn($p) => $p->total_stock * (float)$p->price_usd);
        $totalInventoryValueVes = $totalInventoryValueUsd * $bcvUsdRate;

        $categories = Category::where('tenant_id', $tenant->id)->get();
        $branches = Branch::where('tenant_id', $tenant->id)->get();

        $productsJson = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku ?? '',
                'barcode' => $p->barcode ?? '',
                'category_id' => (string)($p->category_id ?? ''),
                'category_name' => $p->category->name ?? 'General',
                'image_url' => $p->image_url ?? '',
            ];
        })->values()->toJson();

        return view('inventory.index', compact(
            'tenant',
            'products',
            'productsJson',
            'lowStockProducts',
            'lowStockCount',
            'totalProductsCount',
            'totalStockUnits',
            'totalInventoryValueUsd',
            'totalInventoryValueVes',
            'categories',
            'branches',
            'filter',
            'categoryId',
            'search',
            'bcvUsdRate',
            'bcvEurRate'
        ));
    }

    public function store(Request $request)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:4096',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
            'cost_usd' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0.01',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'unit' => 'nullable|string|max:50',
            'has_lots' => 'nullable|boolean',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $imageUrl = '/storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $imageUrl = $validated['image_url'];
        }

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?: 'SKU-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 3)) . '-' . rand(100, 999),
            'barcode' => $validated['barcode'] ?: null,
            'image_url' => $imageUrl,
            'description' => $validated['description'] ?? null,
            'cost_usd' => $validated['cost_usd'],
            'price_usd' => $validated['price_usd'],
            'min_stock_alert' => $validated['min_stock_alert'] ?? 10.00,
            'unit' => $validated['unit'] ?? 'Unidad',
            'has_lots' => $request->has('has_lots'),
            'is_active' => true,
        ]);

        $initialStock = (float) ($request->input('stock_quantity') ?? 0);
        $branchId = $request->input('branch_id') ?: 1;

        InventoryStock::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchId,
            'product_id' => $product->id,
            'quantity' => $initialStock,
        ]);

        return redirect()->route('inventory.index')->with('success', '¡Producto "' . $product->name . '" registrado exitosamente con ' . $initialStock . ' unidades en stock!');
    }

    public function updateStock(Request $request)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'nullable|exists:branches,id',
            'quantity' => 'required|numeric|min:0',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'operation' => 'nullable|string|in:set,add',
        ]);

        $branchId = $validated['branch_id'] ?? 1;
        $stock = InventoryStock::firstOrCreate([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchId,
            'product_id' => $validated['product_id'],
        ], [
            'quantity' => 0
        ]);

        if (($validated['operation'] ?? 'set') === 'add') {
            $stock->quantity += (float) $validated['quantity'];
        } else {
            $stock->quantity = (float) $validated['quantity'];
        }
        $stock->save();

        if (isset($validated['min_stock_alert'])) {
            Product::where('id', $validated['product_id'])->update([
                'min_stock_alert' => $validated['min_stock_alert']
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Stock de producto actualizado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:4096',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
            'cost_usd' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0.01',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'has_lots' => 'nullable|boolean',
        ]);

        $imageUrl = $product->image_url;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $imageUrl = '/storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $imageUrl = $validated['image_url'];
        }

        $product->update([
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?: $product->sku,
            'barcode' => $validated['barcode'] ?: null,
            'image_url' => $imageUrl,
            'description' => $validated['description'] ?? null,
            'cost_usd' => $validated['cost_usd'],
            'price_usd' => $validated['price_usd'],
            'min_stock_alert' => $validated['min_stock_alert'] ?? 10.00,
            'unit' => $validated['unit'] ?? 'Unidad',
            'has_lots' => $request->has('has_lots'),
        ]);

        return redirect()->route('inventory.index')->with('success', '¡Producto "' . $product->name . '" actualizado correctamente!');
    }

    public function destroy($id)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);
        $productName = $product->name;
        $product->delete();

        return redirect()->route('inventory.index')->with('success', 'Producto "' . $productName . '" eliminado del inventario.');
    }

    /**
     * Download Excel template for bulk product import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Productos');

        // Headers
        $headers = ['Nombre *', 'Categoría', 'SKU', 'Código de Barras', 'Costo USD *', 'Precio Venta USD *', 'Stock Inicial', 'Alerta Stock Mínimo', 'Unidad', 'URL Imagen', 'Descripción'];
        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Example rows
        $examples = [
            ['Harina PAN Blanca 1kg', 'Víveres y Granos', 'VIV-001', '7591002005678', 0.95, 1.35, 350, 20, 'Unidad', '', 'Harina de maíz precocida'],
            ['Refresco Coca-Cola 2L', 'Bebidas y Refrescos', 'BEB-001', '7591001001234', 1.80, 2.50, 170, 10, 'Unidad', '', 'Refresco de cola 2 litros'],
            ['Queso Blanco (Kg)', 'Charcutería y Lácteos', 'CHA-001', '7591003009012', 5.20, 7.80, 35, 5, 'Kg', '', 'Queso blanco tipo paisa'],
        ];

        foreach ($examples as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + 2);
                $sheet->setCellValue($cell, $value);
            }
        }

        // Style example rows with light bg
        $exampleStyle = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0FF']],
            'font' => ['italic' => true, 'color' => ['rgb' => '666666']],
        ];
        $sheet->getStyle('A2:K4')->applyFromArray($exampleStyle);

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Instructions sheet
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Instrucciones');

        $instructions = [
            ['📋 INSTRUCCIONES PARA CARGA MASIVA DE PRODUCTOS'],
            [''],
            ['COLUMNA', 'DESCRIPCIÓN', 'OBLIGATORIO', 'EJEMPLO'],
            ['Nombre', 'Nombre del producto tal como se mostrará en el sistema', 'SÍ ✅', 'Harina PAN Blanca 1kg'],
            ['Categoría', 'Nombre exacto de la categoría existente en tu sistema', 'NO', 'Víveres y Granos'],
            ['SKU', 'Código interno único del producto. Si se deja vacío se genera automáticamente', 'NO', 'VIV-001'],
            ['Código de Barras', 'Código de barras EAN/UPC del producto', 'NO', '7591002005678'],
            ['Costo USD', 'Precio de compra/costo en dólares americanos (USD)', 'SÍ ✅', '0.95'],
            ['Precio Venta USD', 'Precio de venta al público en dólares (USD)', 'SÍ ✅', '1.35'],
            ['Stock Inicial', 'Cantidad inicial en inventario. Si se deja vacío = 0', 'NO', '350'],
            ['Alerta Stock Mínimo', 'Nivel mínimo antes de que se active la alerta roja. Por defecto = 10', 'NO', '20'],
            ['Unidad', 'Unidad de medida: Unidad, Kg, Litro, Caja, etc. Por defecto = Unidad', 'NO', 'Unidad'],
            ['URL Imagen', 'Enlace público a una imagen del producto', 'NO', 'https://ejemplo.com/img.jpg'],
            ['Descripción', 'Descripción opcional del producto', 'NO', 'Harina de maíz precocida'],
            [''],
            ['⚠️ IMPORTANTE:'],
            ['• Los campos marcados como obligatorios (SÍ ✅) deben tener un valor en cada fila.'],
            ['• La hoja "Productos" es la que se procesa. No cambies el nombre de las columnas.'],
            ['• Las filas de ejemplo (en gris) puedes borrarlas o sobreescribirlas con tus datos.'],
            ['• Formatos aceptados: .xlsx, .xls, .csv'],
            ['• Máximo recomendado: 500 productos por archivo.'],
        ];

        foreach ($instructions as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + 1);
                $instrSheet->setCellValue($cell, $value);
            }
        }

        // Style instructions
        $instrSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '4F46E5']],
        ]);
        $instrSheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']],
        ]);
        foreach (range('A', 'D') as $col) {
            $instrSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'plantilla_productos_pymora.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Bulk import products from Excel/CSV file
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $tenant = Tenant::current() ?? (object)['id' => 1];
        $file = $request->file('file');

        try {
            $extension = $file->getClientOriginalExtension();
            $reader = match(strtolower($extension)) {
                'csv' => new \PhpOffice\PhpSpreadsheet\Reader\Csv(),
                'xls' => new \PhpOffice\PhpSpreadsheet\Reader\Xls(),
                default => new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(),
            };

            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // Remove header row
            $headerRow = array_shift($rows);

            // Map columns by position
            $categories = Category::where('tenant_id', $tenant->id)->pluck('id', 'name')->toArray();
            $categoriesLower = [];
            foreach ($categories as $name => $id) {
                $categoriesLower[mb_strtolower(trim($name))] = $id;
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $rowIndex => $row) {
                $rowNum = $rowIndex + 1;
                $values = array_values($row);

                $name = trim($values[0] ?? '');
                $categoryName = trim($values[1] ?? '');
                $sku = trim($values[2] ?? '');
                $barcode = trim($values[3] ?? '');
                $costUsd = $values[4] ?? null;
                $priceUsd = $values[5] ?? null;
                $stockQty = $values[6] ?? 0;
                $minAlert = $values[7] ?? 10;
                $unit = trim($values[8] ?? '') ?: 'Unidad';
                $imageUrl = trim($values[9] ?? '');
                $description = trim($values[10] ?? '');

                // Skip completely empty rows
                if (empty($name)) {
                    continue;
                }

                // Validate required fields
                if (!is_numeric($costUsd) || !is_numeric($priceUsd) || (float)$priceUsd <= 0) {
                    $errors[] = "Fila {$rowNum}: \"{$name}\" — Costo o Precio USD inválido.";
                    $skipped++;
                    continue;
                }

                // Resolve category
                $categoryId = null;
                if ($categoryName !== '') {
                    $catKey = mb_strtolower($categoryName);
                    if (isset($categoriesLower[$catKey])) {
                        $categoryId = $categoriesLower[$catKey];
                    } else {
                        // Auto-create category
                        $newCat = Category::create([
                            'tenant_id' => $tenant->id,
                            'name' => $categoryName,
                            'slug' => \Illuminate\Support\Str::slug($categoryName),
                        ]);
                        $categories[$categoryName] = $newCat->id;
                        $categoriesLower[$catKey] = $newCat->id;
                        $categoryId = $newCat->id;
                    }
                }

                $product = Product::create([
                    'tenant_id' => $tenant->id,
                    'category_id' => $categoryId,
                    'name' => $name,
                    'sku' => $sku ?: 'SKU-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3)) . '-' . rand(100, 999),
                    'barcode' => $barcode ?: null,
                    'image_url' => $imageUrl ?: null,
                    'description' => $description ?: null,
                    'cost_usd' => (float)$costUsd,
                    'price_usd' => (float)$priceUsd,
                    'min_stock_alert' => is_numeric($minAlert) ? (float)$minAlert : 10,
                    'unit' => $unit,
                    'is_active' => true,
                ]);

                $initialStock = is_numeric($stockQty) ? (float)$stockQty : 0;
                InventoryStock::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => 1,
                    'product_id' => $product->id,
                    'quantity' => $initialStock,
                ]);

                $imported++;
            }

            $message = "✅ Carga masiva completada: {$imported} productos importados.";
            if ($skipped > 0) {
                $message .= " ⚠️ {$skipped} filas omitidas por errores.";
            }
            if (!empty($errors)) {
                $message .= ' | Errores: ' . implode(' | ', array_slice($errors, 0, 5));
            }

            return redirect()->route('inventory.index')->with('success', $message);
        } catch (Exception $e) {
            return redirect()->route('inventory.index')->with('error', '❌ Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}
