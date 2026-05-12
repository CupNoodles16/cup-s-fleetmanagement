<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'billing_address',
        'billing_city',
        'billing_province',
        'billing_zip',
        'status',
        'credit_limit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
        ];
    }

    // Relationships
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBlacklisted($query)
    {
        return $query->where('status', 'blacklisted');
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBlacklisted(): bool
    {
        return $this->status === 'blacklisted';
    }
}
