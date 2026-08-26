<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaasPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'amount_usd',
        'exchange_rate_bcv',
        'amount_ves',
        'payment_method',
        'reference_code',
        'payment_date',
        'plan_tier',
        'months_paid',
        'notes',
        'proof_image',
    ];

    protected $casts = [
        'amount_usd' => 'decimal:2',
        'exchange_rate_bcv' => 'decimal:4',
        'amount_ves' => 'decimal:2',
        'payment_date' => 'date',
        'months_paid' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
