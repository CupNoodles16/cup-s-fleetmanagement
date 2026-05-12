<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    // High-write table — no updated_at
    public $timestamps = false;

    protected $fillable = [
        'driver_id',
        'load_id',
        'lat',
        'lng',
        'speed_kmh',
        'heading_degrees',
        'accuracy_meters',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'lat'         => 'decimal:7',
            'lng'         => 'decimal:7',
        ];
    }

    // Relationships
    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function parentload(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

    // Scopes
    public function scopeForLoad($query, int $loadId)
    {
        return $query->where('load_id', $loadId);
    }

    public function scopeRecent($query, int $minutes = 5)
    {
        return $query->where('recorded_at', '>=', now()->subMinutes($minutes));
    }
}
