<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'type',
        'file_path',
        'external_url',
        'content_text',
        'duration_minutes',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELACIONES ====================

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ContentProgress::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ==================== HELPERS ====================

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'pdf' => 'file-text',
            'video' => 'play-circle',
            'audio' => 'headphones',
            'link' => 'external-link',
            'text' => 'align-left',
            default => 'file',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'pdf' => 'text-red-500',
            'video' => 'text-blue-500',
            'audio' => 'text-purple-500',
            'link' => 'text-green-500',
            'text' => 'text-gray-500',
            default => 'text-gray-400',
        };
    }

    public function isCompletedBy(User $user): bool
    {
        return $this->progress()
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->exists();
    }

    public function getProgressFor(User $user): ?ContentProgress
    {
        return $this->progress()->where('user_id', $user->id)->first();
    }
}
