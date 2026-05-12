<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plate_number',
        'model',
        'year',
        'type',
        'capacity_kg',
        'status',
        'registration_expiry',
        'insurance_expiry',
        'last_maintenance_date',
        'odometer_km',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'registration_expiry'   => 'date',
            'insurance_expiry'      => 'date',
            'last_maintenance_date' => 'date',
        ];
    }

    // Relationships
    public function drivers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Driver::class, 'current_vehicle_id');
    }

    public function currentDriver(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Driver::class, 'current_vehicle_id')
                    ->where('status', 'on_trip');
    }

    public function loads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Load::class);
    }

    public function activeLoad(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Load::class)
                    ->whereNotIn('status', ['delivered', 'cancelled', 'failed']);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRegistrationExpiringSoon($query, int $days = 30)
    {
        return $query->whereBetween('registration_expiry', [now(), now()->addDays($days)]);
    }

    public function scopeInsuranceExpiringSoon($query, int $days = 30)
    {
        return $query->whereBetween('insurance_expiry', [now(), now()->addDays($days)]);
    }

    // Helpers
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isOnTrip(): bool
    {
        return $this->status === 'on_trip';
    }

    public function canCarry(int $weightKg): bool
    {
        return $this->capacity_kg >= $weightKg;
    }
}
