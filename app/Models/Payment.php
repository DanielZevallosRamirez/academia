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
        'total_installments',
        'notes',
    ];

    /**
     * Get installment label (e.g., "Cuota 1/3")
     */
    public function getInstallmentLabelAttribute(): ?string
    {
        if ($this->installment_number) {
            $total = $this->total_installments ?? 1;
            return "Cuota {$this->installment_number}/{$total}";
        }
        return null;
    }

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

        // Auto-update status based on amount_paid
        static::saving(function ($payment) {
            $amountPaid = $payment->amount_paid ?? 0;
            $amount = $payment->amount ?? 0;

            if ($amount > 0 && $amountPaid >= $amount) {
                $payment->status = 'pagado';
                if (!$payment->paid_at && !$payment->paid_date) {
                    $payment->paid_date = now();
                }
            } elseif ($amountPaid > 0 && $amountPaid < $amount) {
                $payment->status = 'parcial';
            }
            // Keep existing status if amount_paid is 0 (pendiente, vencido, etc.)
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

    public function scopePartial($query)
    {
        return $query->where('status', 'parcial');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pendiente', 'parcial']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'pagado');
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pendiente', 'parcial'])
            ->where('due_date', '<', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // ==================== HELPERS ====================

    /**
     * Get the real/calculated status based on amounts
     * For display purposes - keeps individual payment status for history
     * Only the LAST installment of a complete plan shows as "pagado"
     */
    public function getRealStatusAttribute(): string
    {
        $amountPaid = $this->amount_paid ?? 0;
        $amount = $this->amount ?? 0;

        // If full amount paid, it's pagado
        if ($amount > 0 && $amountPaid >= $amount) {
            return 'pagado';
        }
        
        // Check if this is the LAST installment of a completed plan
        if ($this->isLastInstallmentAndPlanComplete()) {
            return 'pagado';
        }
        
        // Partial payment
        if ($amountPaid > 0 && $amountPaid < $amount) {
            return 'parcial';
        }
        
        // Return stored status for other cases (pendiente, vencido, cancelado)
        return $this->status;
    }

    /**
     * Check if this is the last installment AND ALL installments are fully paid
     */
    public function isLastInstallmentAndPlanComplete(): bool
    {
        if (!$this->installment_number || !$this->total_installments || $this->total_installments <= 1) {
            return false;
        }

        // Must be the last installment number
        if ($this->installment_number < $this->total_installments) {
            return false;
        }

        // Verify ALL installments are fully paid (amount_paid >= amount)
        $paidInstallments = Payment::where('enrollment_id', $this->enrollment_id)
            ->where('concept', $this->concept)
            ->whereColumn('amount_paid', '>=', 'amount')
            ->count();

        return $paidInstallments >= $this->total_installments;
    }

    /**
     * Check if this payment belongs to a completed installment plan
     * A plan is complete when ALL payments in the plan are fully paid (amount_paid >= amount)
     * Used for statistics - excludes from pending counts when plan is complete
     */
    public function belongsToCompletePlan(): bool
    {
        if (!$this->installment_number || !$this->total_installments || $this->total_installments <= 1) {
            return false;
        }

        // Count how many payments are fully paid in this plan (amount_paid >= amount)
        $paidInstallments = Payment::where('enrollment_id', $this->enrollment_id)
            ->where('concept', $this->concept)
            ->whereColumn('amount_paid', '>=', 'amount')
            ->count();

        // Plan is complete only if ALL installments are fully paid
        return $paidInstallments >= $this->total_installments;
    }

    /**
     * Check if this payment should be counted as pending in statistics
     * A payment counts as pending if it has pending/parcial/vencido status
     */
    public function shouldCountAsPending(): bool
    {
        // Only count as pending if status is actually pending/parcial/vencido
        return in_array($this->real_status, ['pendiente', 'parcial', 'vencido']);
    }

    /**
     * Check if all installments for this plan are complete (all paid)
     */
    public function isPlanComplete(): bool
    {
        return $this->belongsToCompletePlan() || $this->real_status === 'pagado';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->real_status) {
            'pendiente' => 'bg-yellow-100 text-yellow-800',
            'parcial' => 'bg-orange-100 text-orange-800',
            'pagado' => 'bg-green-100 text-green-800',
            'vencido' => 'bg-red-100 text-red-800',
            'cancelado' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->real_status) {
            'pendiente' => 'Pendiente',
            'parcial' => 'Parcial',
            'pagado' => 'Pagado',
            'vencido' => 'Vencido',
            'cancelado' => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - ($this->amount_paid ?? 0));
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
