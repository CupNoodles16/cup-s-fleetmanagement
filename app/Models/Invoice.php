<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'order_id',
        'customer_id',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'issued_at',
        'due_at',
        'paid_at',
        'pdf_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'     => 'decimal:2',
            'tax_amount'   => 'decimal:2',
            'total_amount' => 'decimal:2',
            'issued_at'    => 'date',
            'due_at'       => 'date',
            'paid_at'      => 'datetime',
        ];
    }

    // Relationships
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                     ->orWhere(function ($q) {
                         $q->where('status', 'sent')
                           ->where('due_at', '<', now());
                     });
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'overdue']);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Helpers
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->due_at->isPast() && !$this->isPaid();
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);
    }
}
