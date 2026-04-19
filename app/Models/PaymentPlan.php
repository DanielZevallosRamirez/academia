<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'total_installments',
        'installment_amount',
        'day_of_month',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'installment_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELACIONES ====================

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    // ==================== HELPERS ====================

    public function generatePayments(): void
    {
        $enrollment = $this->enrollment;
        
        for ($i = 1; $i <= $this->total_installments; $i++) {
            $dueDate = $enrollment->start_date
                ->copy()
                ->addMonths($i - 1)
                ->day($this->day_of_month);

            Payment::create([
                'user_id' => $enrollment->user_id,
                'enrollment_id' => $enrollment->id,
                'amount' => $this->installment_amount,
                'due_date' => $dueDate,
                'status' => 'pendiente',
            ]);
        }
    }
}
