<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'sequence',
        'type',
        'address_line',
        'city',
        'province',
        'lat',
        'lng',
        'contact_name',
        'contact_phone',
        'scheduled_at',
        'arrived_at',
        'completed_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'arrived_at'   => 'datetime',
            'completed_at' => 'datetime',
            'lat'          => 'decimal:7',
            'lng'          => 'decimal:7',
        ];
    }

    // Relationships
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function podMedia(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PodMedia::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePickups($query)
    {
        return $query->where('type', 'pickup');
    }

    public function scopeDeliveries($query)
    {
        return $query->where('type', 'delivery');
    }

    // Helpers
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPickup(): bool
    {
        return $this->type === 'pickup';
    }

    public function isDelivery(): bool
    {
        return $this->type === 'delivery';
    }

    public function fullAddress(): string
    {
        return implode(', ', array_filter([
            $this->address_line,
            $this->city,
            $this->province,
        ]));
    }
}
