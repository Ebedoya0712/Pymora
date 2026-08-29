<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expiration_date' => 'date',
        'manufactured_date' => 'date',
        'quantity' => 'float',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        if (!$this->expiration_date) return 999;
        return (int) now()->startOfDay()->diffInDays($this->expiration_date->startOfDay(), false);
    }

    public function getStatusBadgeAttribute(): array
    {
        $days = $this->days_until_expiration;

        if ($days < 0) {
            return [
                'label' => 'Vencido (' . abs($days) . ' días)',
                'color' => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
                'icon' => '🚨',
                'severity' => 'expired'
            ];
        } elseif ($days <= 15) {
            return [
                'label' => 'Crítico (' . $days . ' días)',
                'color' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                'icon' => '⚠️',
                'severity' => 'critical'
            ];
        } elseif ($days <= 30) {
            return [
                'label' => 'Por Vencer (' . $days . ' días)',
                'color' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                'icon' => '⏳',
                'severity' => 'warning'
            ];
        } else {
            return [
                'label' => 'Vigente (' . $days . ' días)',
                'color' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                'icon' => '✓',
                'severity' => 'good'
            ];
        }
    }
}
