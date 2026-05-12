<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Load extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'load_number',
        'order_id',
        'driver_id',
        'vehicle_id',
        'assigned_by',
        'status',
        'assignment_type',
        'assigned_at',
        'driver_accepted_at',
        'pickup_arrived_at',
        'pickup_departed_at',
        'delivery_arrived_at',
        'delivered_at',
        'cancelled_at',
        'eta_at',
        'is_delayed',
        'delay_minutes',
        'bol_path',
        'pod_signature_path',
        'pod_captured_at',
        'pod_captured_by',
        'driver_notes',
        'cancellation_reason',
        'manual_override_reason',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at'        => 'datetime',
            'driver_accepted_at' => 'datetime',
            'pickup_arrived_at'  => 'datetime',
            'pickup_departed_at' => 'datetime',
            'delivery_arrived_at'=> 'datetime',
            'delivered_at'       => 'datetime',
            'cancelled_at'       => 'datetime',
            'eta_at'             => 'datetime',
            'pod_captured_at'    => 'datetime',
            'is_delayed'         => 'boolean',
        ];
    }

    // Relationships
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function podCapturedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'pod_captured_by');
    }

    public function statusLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoadStatusLog::class)->orderBy('created_at');
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverLocation::class);
    }

    public function latestLocation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DriverLocation::class)->latestOfMany('recorded_at');
    }

    public function podMedia(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PodMedia::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoadComment::class)->orderBy('created_at');
    }

    public function driverComments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoadComment::class)
                    ->where('visibility', 'driver')
                    ->orderBy('created_at');
    }

    // Scopes
    public function scopeUnassigned($query)
    {
        return $query->where('status', 'unassigned');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['delivered', 'cancelled', 'failed']);
    }

    public function scopeDelayed($query)
    {
        return $query->where('is_delayed', true);
    }

    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeManuallyAssigned($query)
    {
        return $query->where('assignment_type', 'manual');
    }

    // Helpers
    public function isAssigned(): bool
    {
        return !in_array($this->status, ['unassigned', 'cancelled', 'failed']);
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['delivered', 'cancelled', 'failed']);
    }
}
