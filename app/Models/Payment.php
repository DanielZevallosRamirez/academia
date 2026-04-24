<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'enrollment_id',
        'invoice_number',
        'concept',
        'amount',
        'amount_paid',
        'payment_method',
        'status',
        'due_date',
        'paid_date',
        'transaction_id',
        'receipt_path',
        'payment_proof',
        'installment_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->invoice_number)) {
                $payment->invoice_number = 'INV-' . strtoupper(Str::random(8));
            }
        });
    }

    // ==================== RELACIONES ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PaymentDocument::class);
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'pagado');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pendiente')
            ->where('due_date', '<', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // ==================== HELPERS ====================

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pendiente' => 'bg-yellow-100 text-yellow-800',
            'pagado' => 'bg-green-100 text-green-800',
            'vencido' => 'bg-red-100 text-red-800',
            'cancelado' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendiente' => 'Pendiente',
            'pagado' => 'Pagado',
            'vencido' => 'Vencido',
            'cancelado' => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_path ? asset('storage/' . $this->receipt_path) : null;
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof ? asset('storage/' . $this->payment_proof) : null;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pendiente' && $this->due_date < today();
    }

    public function markAsPaid(string $method = 'manual', ?string $transactionId = null): void
    {
        $this->update([
            'status' => 'pagado',
            'paid_date' => today(),
            'payment_method' => $method,
            'transaction_id' => $transactionId,
        ]);
    }
}
