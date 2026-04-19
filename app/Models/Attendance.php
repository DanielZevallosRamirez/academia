<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_session_id',
        'user_id',
        'status',
        'check_in_time',
        'check_in_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_time' => 'datetime',
        ];
    }

    // ==================== RELACIONES ====================

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== SCOPES ====================

    public function scopePresent($query)
    {
        return $query->where('status', 'presente');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'ausente');
    }

    public function scopeByStudent($query, int $studentId)
    {
        return $query->where('user_id', $studentId);
    }

    public function scopeByQr($query)
    {
        return $query->where('check_in_method', 'qr');
    }

    // ==================== HELPERS ====================

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'presente' => 'bg-green-100 text-green-800',
            'ausente' => 'bg-red-100 text-red-800',
            'tardanza' => 'bg-yellow-100 text-yellow-800',
            'justificado' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'presente' => 'Presente',
            'ausente' => 'Ausente',
            'tardanza' => 'Tardanza',
            'justificado' => 'Justificado',
            default => 'Desconocido',
        };
    }

    public function markAsPresent(): void
    {
        $this->update([
            'status' => 'presente',
            'check_in_time' => now(),
        ]);
    }
}
