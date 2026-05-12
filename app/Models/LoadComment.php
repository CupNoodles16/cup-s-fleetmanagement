<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadComment extends Model
{
    protected $fillable = [
        'load_id',
        'user_id',
        'visibility',
        'message',
    ];

    // Relationships
    public function parentload(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeInternal($query)
    {
        return $query->where('visibility', 'internal');
    }

    public function scopeVisibleToDriver($query)
    {
        return $query->where('visibility', 'driver');
    }

    // Helpers
    public function isVisibleToDriver(): bool
    {
        return $this->visibility === 'driver';
    }
}
