<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'name',
        'slug',
        'description',
        'order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            // status es string enum: activo, inactivo
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->name);
            }
        });
    }

    // ==================== RELACIONES ====================

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function professors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_professor')
            ->withTimestamps();
    }

    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    // ==================== HELPERS ====================

    public function getTotalContentsAttribute(): int
    {
        return $this->modules->sum(function ($module) {
            return $module->contents->count();
        });
    }

    public function getTotalDurationAttribute(): int
    {
        return $this->modules->sum(function ($module) {
            return $module->contents->sum('duration_minutes') ?? 0;
        });
    }
}
