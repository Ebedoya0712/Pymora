<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\InventoryStock;
use App\Models\Customer;
use App\Models\CashRegister;
use App\Models\CashSession;
use App\Models\BankAccount;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\StockTransfer;
use App\Models\TaxRetention;
use App\Models\SellerCommission;
use App\Models\SaasPayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin (Global SaaS Owner)
        $superAdmin = User::create([
            'tenant_id' => null,
            'branch_id' => null,
            'name' => 'Super Admin Pymora',
            'email' => 'admin@pymora.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'phone' => '+584120000000',
            'is_active' => true,
        ]);

        // 2. Demo Tenant
        $tenant = Tenant::create([
            'name' => 'Bodega & Abasto El Sol C.A.',
            'rif_tax_id' => 'J-12345678-9',
            'subdomain' => 'elsol',
            'plan_tier' => 'pro',
            'is_active' => true,
            'phone' => '+584141234567',
            'email' => 'contacto@elsol.com.ve',
            'address' => 'Av. Francisco de Miranda, Edif. Centro Altamira, Piso 2, Caracas',
            'currency_primary' => 'USD',
            'currency_secondary' => 'VES',
            'bcv_rate' => 52.4000,
            'parallel_rate' => 54.1000,
            'igtf_percentage' => 3.00,
            'expires_at' => now()->addDays(365),
        ]);

        // 3. Branches
        $branchMain = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Sucursal Principal Altamira',
            'code' => 'ALT-001',
            'phone' => '+584141234567',
            'address' => 'Altamira Sur, Caracas',
            'is_main' => true,
            'is_active' => true,
        ]);

        $branchSecondary = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Almacén Central Las Mercedes',
            'code' => 'MER-002',
            'phone' => '+584129876543',
            'address' => 'Calle Madrid, Las Mercedes, Caracas',
            'is_main' => false,
            'is_active' => true,
        ]);

        // 4. Tenant Users (Roles)
        $owner = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'name' => 'Carlos Mendoza (Dueño)',
            'email' => 'carlos@elsol.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'phone' => '+584141112233',
            'is_active' => true,
        ]);

        $manager = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'name' => 'María Rodríguez (Gerente)',
            'email' => 'maria@elsol.com',
            'password' => Hash::make('password123'),
            'role' => 'branch_manager',
            'phone' => '+584142223344',
            'is_active' => true,
        ]);

        $cashier = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'name' => 'Pedro Gómez (Cajero)',
            'email' => 'pedro@elsol.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
            'phone' => '+584143334455',
            'is_active' => true,
        ]);

        $warehouse = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchSecondary->id,
            'name' => 'Luis Hernández (Almacén)',
            'email' => 'luis@elsol.com',
            'password' => Hash::make('password123'),
            'role' => 'warehouse_manager',
            'phone' => '+584144445566',
            'is_active' => true,
        ]);

        $accountant = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'name' => 'Ana Silva (Contadora)',
            'email' => 'ana@elsol.com',
            'password' => Hash::make('password123'),
            'role' => 'accountant',
            'phone' => '+584145556677',
            'is_active' => true,
        ]);

        // 5. Categories
        $catBebidas = Category::create(['tenant_id' => $tenant->id, 'name' => 'Bebidas y Refrescos', 'slug' => 'bebidas', 'icon' => 'cup-straw']);
        $catViveres = Category::create(['tenant_id' => $tenant->id, 'name' => 'Víveres y Granos', 'slug' => 'viveres', 'icon' => 'shopping-bag']);
        $catCharcuteria = Category::create(['tenant_id' => $tenant->id, 'name' => 'Charcutería y Lácteos', 'slug' => 'charcuteria', 'icon' => 'cheese']);
        $catLimpieza = Category::create(['tenant_id' => $tenant->id, 'name' => 'Limpieza e Higiene', 'slug' => 'limpieza', 'icon' => 'sparkles']);

        // 6. Products
        $p1 = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $catBebidas->id,
            'name' => 'Refresco Coca-Cola 2L',
            'sku' => 'BEB-001',
            'barcode' => '7591001001234',
            'unit' => 'Unidad',
            'cost_usd' => 1.80,
            'price_usd' => 2.50,
            'tax_rate' => 16.00,
            'min_stock_alert' => 10,
        ]);

        $p2 = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $catViveres->id,
            'name' => 'Harina PAN Blanca 1kg',
            'sku' => 'VIV-001',
            'barcode' => '7591002005678',
            'unit' => 'Unidad',
            'cost_usd' => 0.95,
            'price_usd' => 1.35,
            'tax_rate' => 16.00,
            'min_stock_alert' => 20,
        ]);

        $p3 = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $catCharcuteria->id,
            'name' => 'Queso Paisa Blanco (Kg)',
            'sku' => 'CHA-001',
            'barcode' => '7591003009012',
            'unit' => 'Kg',
            'cost_usd' => 5.20,
            'price_usd' => 7.80,
            'tax_rate' => 16.00,
            'has_lots' => true,
            'min_stock_alert' => 5,
        ]);

        $p4 = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $catViveres->id,
            'name' => 'Arroz Primor Supremo 1kg',
            'sku' => 'VIV-002',
            'barcode' => '7591004003456',
            'unit' => 'Unidad',
            'cost_usd' => 1.10,
            'price_usd' => 1.50,
            'tax_rate' => 16.00,
            'min_stock_alert' => 15,
        ]);

        // 7. Inventory Stocks
        InventoryStock::create(['tenant_id' => $tenant->id, 'branch_id' => $branchMain->id, 'product_id' => $p1->id, 'quantity' => 120]);
        InventoryStock::create(['tenant_id' => $tenant->id, 'branch_id' => $branchMain->id, 'product_id' => $p2->id, 'quantity' => 250]);
        InventoryStock::create(['tenant_id' => $tenant->id, 'branch_id' => $branchMain->id, 'product_id' => $p3->id, 'quantity' => 35.5, 'lot_number' => 'LOT-2026-08', 'expiration_date' => now()->addMonths(2)]);
        InventoryStock::create(['tenant_id' => $tenant->id, 'branch_id' => $branchMain->id, 'product_id' => $p4->id, 'quantity' => 180]);

        InventoryStock::create(['tenant_id' => $tenant->id, 'branch_id' => $branchSecondary->id, 'product_id' => $p1->id, 'quantity' => 50]);
        InventoryStock::create(['tenant_id' => $tenant->id, 'branch_id' => $branchSecondary->id, 'product_id' => $p2->id, 'quantity' => 100]);

        // 8. Customers
        $c1 = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Inversiones Los Chaguaramos C.A.',
            'tax_id' => 'J-30987654-1',
            'email' => 'compras@loschaguaramos.com',
            'phone' => '+584123334455',
            'address' => 'Calle Los Chaguaramos, Edif 4, Caracas',
            'customer_type' => 'b2b',
            'credit_limit_usd' => 1000.00,
            'current_debt_usd' => 150.00,
        ]);

        $c2 = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Juan Pérez (Cliente Detal)',
            'tax_id' => 'V-18234567',
            'phone' => '+584241112233',
            'customer_type' => 'retail',
            'credit_limit_usd' => 0.00,
            'current_debt_usd' => 0.00,
        ]);

        // 9. Bank Accounts
        BankAccount::create(['tenant_id' => $tenant->id, 'name' => 'Banesco Bolívares Principal', 'bank_name' => 'Banesco', 'account_number' => '0134-0001-00-1234567890', 'currency' => 'VES', 'balance' => 45000.00]);
        BankAccount::create(['tenant_id' => $tenant->id, 'name' => 'Zelle Dólares Empresa', 'bank_name' => 'Chase Bank', 'account_number' => 'pagos@elsol.com', 'currency' => 'USD', 'balance' => 3200.00]);
        BankAccount::create(['tenant_id' => $tenant->id, 'name' => 'Efectivo Caja USD Altamira', 'bank_name' => 'Caja Chica', 'account_number' => 'CAJA-USD', 'currency' => 'USD', 'balance' => 850.00]);

        // 10. Cash Register & Session
        $cashRegister = CashRegister::create(['tenant_id' => $tenant->id, 'branch_id' => $branchMain->id, 'name' => 'Caja POS 01', 'is_active' => true]);
        $cashSession = CashSession::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'cash_register_id' => $cashRegister->id,
            'user_id' => $cashier->id,
            'opened_at' => now()->startOfDay(),
            'initial_cash_usd' => 50.00,
            'initial_cash_ves' => 1000.00,
            'expected_cash_usd' => 250.00,
            'expected_cash_ves' => 5000.00,
            'status' => 'open',
        ]);

        // 11. Sample Sales
        $sale = Sale::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'cash_session_id' => $cashSession->id,
            'user_id' => $cashier->id,
            'customer_id' => $c2->id,
            'sale_number' => 'VTA-2026-0001',
            'status' => 'completed',
            'subtotal_usd' => 7.70,
            'tax_usd' => 1.23,
            'total_usd' => 8.93,
            'exchange_rate_bcv' => 52.4000,
            'total_ves' => 467.93,
            'payment_status' => 'paid',
        ]);

        SaleItem::create(['sale_id' => $sale->id, 'product_id' => $p1->id, 'product_name' => $p1->name, 'quantity' => 2, 'unit_price_usd' => 2.50, 'tax_rate' => 16.00, 'subtotal_usd' => 5.00]);
        SaleItem::create(['sale_id' => $sale->id, 'product_id' => $p2->id, 'product_name' => $p2->name, 'quantity' => 2, 'unit_price_usd' => 1.35, 'tax_rate' => 16.00, 'subtotal_usd' => 2.70]);

        Payment::create([
            'tenant_id' => $tenant->id,
            'sale_id' => $sale->id,
            'payment_method' => 'cash_usd',
            'currency' => 'USD',
            'amount_usd' => 8.93,
            'amount_native' => 8.93,
            'exchange_rate' => 52.4000,
            'reference_code' => 'EFECTIVO-USD',
        ]);

        // 12. Sample Quote
        Quote::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchMain->id,
            'customer_id' => $c1->id,
            'user_id' => $owner->id,
            'approved_by' => $owner->id,
            'quote_number' => 'COT-2026-001',
            'valid_until' => now()->addDays(15),
            'status' => 'approved',
            'subtotal_usd' => 150.00,
            'tax_usd' => 24.00,
            'total_usd' => 174.00,
            'items' => [
                ['product_name' => 'Harina PAN Blanca 1kg', 'qty' => 50, 'unit_price' => 1.35],
                ['product_name' => 'Refresco Coca-Cola 2L', 'qty' => 33, 'unit_price' => 2.50]
            ],
            'notes' => 'Presupuesto corporativo aprobado para Inversiones Los Chaguaramos.',
        ]);

        // 13. Sample Stock Transfer
        StockTransfer::create([
            'tenant_id' => $tenant->id,
            'from_branch_id' => $branchSecondary->id,
            'to_branch_id' => $branchMain->id,
            'user_id' => $warehouse->id,
            'transfer_number' => 'TRF-2026-001',
            'status' => 'in_transit',
            'items' => [
                ['product_name' => 'Harina PAN Blanca 1kg', 'quantity' => 50]
            ],
            'notes' => 'Reposición de mercancía por alta demanda en Altamira.',
        ]);

        // 14. Sample Tax Retention (SENIAT)
        TaxRetention::create([
            'tenant_id' => $tenant->id,
            'retention_number' => '2026080000001',
            'type' => 'iva_75',
            'supplier_name' => 'Distribuidora Polar C.A.',
            'supplier_tax_id' => 'J-00001234-5',
            'invoice_number' => 'FAC-998877',
            'base_amount_usd' => 500.00,
            'tax_amount_usd' => 80.00,
            'retained_amount_usd' => 60.00,
            'retention_date' => now()->subDays(2),
        ]);

        // 15. Seller Commission
        SellerCommission::create([
            'tenant_id' => $tenant->id,
            'user_id' => $cashier->id,
            'sale_id' => $sale->id,
            'commission_rate' => 3.00,
            'commission_amount_usd' => 0.27,
            'status' => 'pending',
        ]);

        // 16. SaaS Subscription Payments
        $seedBcvRate = 52.40;

        SaasPayment::create([
            'tenant_id' => $tenant->id,
            'amount_usd' => 158.00,
            'exchange_rate_bcv' => $seedBcvRate,
            'amount_ves' => round(158.00 * $seedBcvRate, 2),
            'payment_method' => 'pago_movil',
            'reference_code' => 'PM-883921',
            'payment_date' => now()->toDateString(),
            'plan_tier' => 'pro',
            'months_paid' => 2,
            'notes' => 'Pago recibido vía Pago Móvil Banesco 0414-1112233.',
            'receipt_image' => '/uploads/receipts/pago_movil_sample.png',
        ]);

        SaasPayment::create([
            'tenant_id' => $tenant->id,
            'amount_usd' => 79.00,
            'exchange_rate_bcv' => $seedBcvRate,
            'amount_ves' => round(79.00 * $seedBcvRate, 2),
            'payment_method' => 'binance',
            'reference_code' => 'BIN-772910',
            'payment_date' => now()->subDays(1)->toDateString(),
            'plan_tier' => 'pro',
            'months_paid' => 1,
            'notes' => 'Abono verificado vía Binance Pay (USDT).',
            'receipt_image' => '/uploads/receipts/pago_movil_sample.png',
        ]);

        SaasPayment::create([
            'tenant_id' => $tenant->id,
            'amount_usd' => 29.00,
            'exchange_rate_bcv' => $seedBcvRate,
            'amount_ves' => round(29.00 * $seedBcvRate, 2),
            'payment_method' => 'paypal',
            'reference_code' => 'PP-994821',
            'payment_date' => now()->subDays(2)->toDateString(),
            'plan_tier' => 'starter',
            'months_paid' => 1,
            'notes' => 'Pago de suscripción Plan Sencillo vía PayPal Checkout.',
            'receipt_image' => '/uploads/receipts/pago_movil_sample.png',
        ]);

        SaasPayment::create([
            'tenant_id' => $tenant->id,
            'amount_usd' => 79.00,
            'exchange_rate_bcv' => $seedBcvRate,
            'amount_ves' => round(79.00 * $seedBcvRate, 2),
            'payment_method' => 'zinli',
            'reference_code' => 'ZIN-332910',
            'payment_date' => now()->subDays(4)->toDateString(),
            'plan_tier' => 'pro',
            'months_paid' => 1,
            'notes' => 'Comprobante recibido mediante Zinli Wallet.',
            'receipt_image' => '/uploads/receipts/pago_movil_sample.png',
        ]);
    }
}
