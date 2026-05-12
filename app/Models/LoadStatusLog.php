<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadStatusLog extends Model
{
    // Append-only — no updated_at needed
    public $timestamps = false;

    protected $fillable = [
        'load_id',
        'from_status',
        'to_status',
        'changed_by',
        'source',
        'lat',
        'lng',
        'notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'lat'        => 'decimal:7',
            'lng'        => 'decimal:7',
        ];
    }

    // Relationships
    public function parentload(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

    public function changedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeFromDriver($query)
    {
        return $query->where('source', 'driver_app');
    }

    public function scopeFromDispatcher($query)
    {
        return $query->where('source', 'dispatcher');
    }
}
