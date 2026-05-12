<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'current_vehicle_id',
        'current_load_id',
        'license_number',
        'license_type',
        'license_expiry',
        'status',
        'hos_remaining_minutes',
        'last_lat',
        'last_lng',
        'last_location_at',
        'performance_rating',
        'total_deliveries',
        'phone',
        'emergency_contact',
        'emergency_phone',
        'fcm_token',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'license_expiry'       => 'date',
            'last_location_at'     => 'datetime',
            'performance_rating'   => 'decimal:2',
            'last_lat'             => 'decimal:7',
            'last_lng'             => 'decimal:7',
        ];
    }

    // Relationships
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentVehicle(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    public function currentLoad(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Load::class, 'current_load_id');
    }

    public function loads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Load::class);
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverLocation::class);
    }

    public function latestLocation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DriverLocation::class)->latestOfMany('recorded_at');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                     ->whereNull('current_load_id');
    }

    public function scopeOnTrip($query)
    {
        return $query->where('status', 'on_trip');
    }

    public function scopeCanHandleLoad($query, int $estimatedMinutes)
    {
        return $query->where('hos_remaining_minutes', '>', $estimatedMinutes);
    }

    public function scopeLicenseExpiringSoon($query, int $days = 30)
    {
        return $query->whereBetween('license_expiry', [now(), now()->addDays($days)]);
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->status === 'available' && is_null($this->current_load_id);
    }

    public function isOnTrip(): bool
    {
        return $this->status === 'on_trip';
    }

    public function hasEnoughHos(int $estimatedMinutes): bool
    {
        return $this->hos_remaining_minutes > $estimatedMinutes;
    }

    public function hasFcmToken(): bool
    {
        return !is_null($this->fcm_token);
    }
}
