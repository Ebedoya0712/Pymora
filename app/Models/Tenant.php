<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'bcv_rate' => 'decimal:4',
        'parallel_rate' => 'decimal:4',
        'igtf_percentage' => 'decimal:2',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function saasPayments(): HasMany
    {
        return $this->hasMany(SaasPayment::class);
    }

    public static function getBusinessTypes(): array
    {
        return [
            'abasto' => [
                'id' => 'abasto',
                'name' => 'Abasto & Supermercado',
                'icon' => '🛒',
                'description' => 'Venta rápida al detal, lector de barras, báscula y control de expiración.',
                'badge_color' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
            ],
            'articulos' => [
                'id' => 'articulos',
                'name' => 'Tienda de Artículos & Variedades',
                'icon' => '🛍️',
                'description' => 'Accesorios, regalos, combos, promociones y margen por categoría.',
                'badge_color' => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
            ],
            'restaurante' => [
                'id' => 'restaurante',
                'name' => 'Restaurante & Comida Rápida',
                'icon' => '🍽️',
                'description' => 'Gestión visual de mesas, comandero digital y recetas/descuento de insumos.',
                'badge_color' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
            ],
            'ropa' => [
                'id' => 'ropa',
                'name' => 'Tienda de Ropa & Calzado',
                'icon' => '👗',
                'description' => 'Matriz de variantes por Talla, Color y Marca con etiquetas asociadas.',
                'badge_color' => 'bg-pink-500/20 text-pink-300 border-pink-500/30',
            ],
            'distribuidor' => [
                'id' => 'distribuidor',
                'name' => 'Distribuidor & Mayorista',
                'icon' => '🚚',
                'description' => 'Precios escalonados (Detal/Mayor), rutas de despacho y comisiones de vendedores.',
                'badge_color' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
            ],
            'fabricante' => [
                'id' => 'fabricante',
                'name' => 'Fabricante & Ensamblador',
                'icon' => '🏭',
                'description' => 'Órdenes de producción, recetas de fabricación (BOM) y costo de mermas.',
                'badge_color' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
            ],
            'licoreria' => [
                'id' => 'licoreria',
                'name' => 'Licorería & Bodegón',
                'icon' => '🍾',
                'description' => 'Venta por botellas/cajas, IGTF, licencias de expendio e impuestos de licores.',
                'badge_color' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
            ],
            'repuestos' => [
                'id' => 'repuestos',
                'name' => 'Repuestos & Automotriz',
                'icon' => '🔧',
                'description' => 'Catálogo por Marca/Modelo/Año de vehículo, piezas compatibles y códigos OEM.',
                'badge_color' => 'bg-orange-500/20 text-orange-300 border-orange-500/30',
            ],
            'carniceria_hortalizas' => [
                'id' => 'carniceria_hortalizas',
                'name' => 'Carnicería & Hortalizas',
                'icon' => '🥩',
                'description' => 'Venta por peso en balanza (Kilos/Gramos), desposte de res y mermas.',
                'badge_color' => 'bg-red-500/20 text-red-300 border-red-500/30',
            ],
            'tecnologia_electro' => [
                'id' => 'tecnologia_electro',
                'name' => 'Tecnología & Electrodomésticos',
                'icon' => '💻',
                'description' => 'Captura obligatoria de Seriales/IMEI, certificados de garantía y servicio técnico.',
                'badge_color' => 'bg-teal-500/20 text-teal-300 border-teal-500/30',
            ],
            'servicios' => [
                'id' => 'servicios',
                'name' => 'Negocio de Servicios & Citas',
                'icon' => '🛠️',
                'description' => 'Órdenes de trabajo, agenda de citas, facturación de mano de obra y repuestos.',
                'badge_color' => 'bg-violet-500/20 text-violet-300 border-violet-500/30',
            ],
        ];
    }
}
