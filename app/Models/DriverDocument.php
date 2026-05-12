<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DriverDocument extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'driver_id',
        'type',
        'disk',
        'path',
        'original_filename',
        'mime_type',
        'size_bytes',
        'expires_at',
        'is_verified',
        'verified_by',
        'verified_at',
        'uploaded_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'  => 'date',
            'verified_at' => 'datetime',
            'created_at'  => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    // Relationships
    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function verifiedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function uploadedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expires_at')
                     ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '<', now());
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Helpers
    public function url(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);
        return $disk->url($this->path);
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? false;
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at?->isBetween(now(), now()->addDays($days)) ?? false;
    }

    public function verify(int $userId): void
    {
        $this->update([
            'is_verified' => true,
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }
}
