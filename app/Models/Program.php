<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'price',
        'duration_months',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($program) {
            if (empty($program->slug)) {
                $program->slug = Str::slug($program->name);
            }
        });
    }

    // ==================== RELACIONES ====================

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['start_date', 'end_date', 'status', 'notes'])
            ->withTimestamps();
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==================== HELPERS ====================

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getTotalModulesAttribute(): int
    {
        return $this->courses->sum(function ($course) {
            return $course->modules->count();
        });
    }

    public function getTotalContentsAttribute(): int
    {
        return $this->courses->sum(function ($course) {
            return $course->modules->sum(function ($module) {
                return $module->contents->count();
            });
        });
    }

    public function getActiveStudentsCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'activo')->count();
    }
}
