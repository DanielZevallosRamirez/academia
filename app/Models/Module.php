<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            if (empty($module->slug)) {
                $module->slug = Str::slug($module->name);
            }
        });
    }

    // ==================== RELACIONES ====================

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class)->orderBy('order');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==================== HELPERS ====================

    public function getTotalDurationAttribute(): int
    {
        return $this->contents->sum('duration_minutes') ?? 0;
    }

    public function getCompletionPercentage(User $user): float
    {
        $totalContents = $this->contents->count();
        if ($totalContents === 0) return 0;

        $completedContents = ContentProgress::where('user_id', $user->id)
            ->whereIn('content_id', $this->contents->pluck('id'))
            ->where('completed', true)
            ->count();

        return round(($completedContents / $totalContents) * 100, 1);
    }
}
