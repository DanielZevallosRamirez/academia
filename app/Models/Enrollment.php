<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentPlan(): HasOne
    {
        return $this->hasOne(PaymentPlan::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('status', 'activo')
            ->where('end_date', '<=', now()->addDays($days));
    }

    // ==================== HELPERS ====================

    public function isActive(): bool
    {
        return $this->status === 'activo' && $this->end_date >= now();
    }

    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->where('status', 'pagado')->sum('amount');
    }

    public function getPendingAmountAttribute(): float
    {
        return $this->program->price - $this->total_paid;
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->program->price <= 0) return 100;
        return round(($this->total_paid / $this->program->price) * 100, 1);
    }
}
