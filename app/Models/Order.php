<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'created_by',
        'cargo_description',
        'cargo_type',
        'weight_kg',
        'volume_cbm',
        'required_vehicle_type',
        'status',
        'priority',
        'pickup_scheduled_at',
        'delivery_deadline_at',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'quoted_amount',
        'final_amount',
        'payment_status',
        'cancellation_reason',
        'cancelled_by',
        'tracking_token',
        'special_instructions',
        'intake_channel',
        'external_reference',
    ];

    protected function casts(): array
    {
        return [
            'pickup_scheduled_at'   => 'datetime',
            'delivery_deadline_at'  => 'datetime',
            'confirmed_at'          => 'datetime',
            'completed_at'          => 'datetime',
            'cancelled_at'          => 'datetime',
            'quoted_amount'         => 'decimal:2',
            'final_amount'          => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->tracking_token)) {
                $order->tracking_token = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function stops(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderStop::class)->orderBy('sequence');
    }

    public function loads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Load::class);
    }

    public function activeLoad(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Load::class)
                    ->whereNotIn('status', ['cancelled', 'failed'])
                    ->latestOfMany();
    }

    public function invoice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'confirmed']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'in_transit', 'partially_delivered']);
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['urgent', 'critical']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('delivery_deadline_at', '<', now())
                     ->whereNotIn('status', ['delivered', 'cancelled', 'failed']);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Helpers
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isOverdue(): bool
    {
        return $this->delivery_deadline_at?->isPast()
            && !in_array($this->status, ['delivered', 'cancelled', 'failed']);
    }

    public function trackingUrl(): string
    {
        return url("/track/{$this->tracking_token}");
    }
}
