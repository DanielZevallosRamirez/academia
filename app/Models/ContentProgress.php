<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentProgress extends Model
{
    use HasFactory;

    protected $table = 'content_progress';

    protected $fillable = [
        'user_id',
        'content_id',
        'completed',
        'progress_percent',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    // ==================== RELACIONES ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    // ==================== HELPERS ====================

    public function markAsCompleted(): void
    {
        $this->update([
            'completed' => true,
            'progress_percent' => 100,
            'completed_at' => now(),
        ]);
    }

    public function updateProgress(int $percent): void
    {
        $this->update([
            'progress_percent' => min(100, max(0, $percent)),
            'completed' => $percent >= 100,
            'completed_at' => $percent >= 100 ? now() : null,
        ]);
    }
}
