<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'professor_id',
        'title',
        'description',
        'session_date',
        'start_time',
        'end_time',
        'location',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    // ==================== RELACIONES ====================

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // ==================== SCOPES ====================

    public function scopeToday($query)
    {
        return $query->whereDate('session_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', today())
            ->where('status', 'programada')
            ->orderBy('session_date')
            ->orderBy('start_time');
    }

    public function scopeByProfessor($query, int $professorId)
    {
        return $query->where('professor_id', $professorId);
    }

    // ==================== HELPERS ====================

    public function getAttendanceStats(): array
    {
        $attendances = $this->attendances;
        
        return [
            'total' => $attendances->count(),
            'presente' => $attendances->where('status', 'presente')->count(),
            'ausente' => $attendances->where('status', 'ausente')->count(),
            'tardanza' => $attendances->where('status', 'tardanza')->count(),
            'justificado' => $attendances->where('status', 'justificado')->count(),
        ];
    }

    public function getAttendanceRate(): float
    {
        $total = $this->attendances->count();
        if ($total === 0) return 0;

        $present = $this->attendances->whereIn('status', ['presente', 'tardanza'])->count();
        return round(($present / $total) * 100, 1);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'en_curso';
    }
}
