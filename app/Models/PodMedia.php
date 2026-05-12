<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PodMedia extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'load_id',
        'order_stop_id',
        'type',
        'disk',
        'path',
        'original_filename',
        'size_bytes',
        'mime_type',
        'uploaded_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // Relationships
    public function parentload(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

    public function orderStop(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderStop::class);
    }

    public function uploadedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopeSignatures($query)
    {
        return $query->where('type', 'signature');
    }

    public function scopePhotos($query)
    {
        return $query->where('type', 'photo');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    // Helpers
    public function url(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);
        return $disk->url($this->path);
    }

    public function isSignature(): bool
    {
        return $this->type === 'signature';
    }

    public function isPhoto(): bool
    {
        return $this->type === 'photo';
    }
}
