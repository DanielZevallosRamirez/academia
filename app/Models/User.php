<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'dni',
        'address',
        'emergency_contact',
        'emergency_phone',
        'photo',
        'qr_code',
        'birth_date',
        'specialty',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->qr_code)) {
                $user->qr_code = Str::uuid()->toString();
            }
        });
    }

    // ==================== ROLES ====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProfesor(): bool
    {
        return $this->role === 'profesor';
    }

    public function isEstudiante(): bool
    {
        return $this->role === 'estudiante';
    }

    // ==================== RELACIONES ====================

    /**
     * Inscripciones del estudiante
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Programas en los que está inscrito el estudiante
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'enrollments')
            ->withPivot(['start_date', 'end_date', 'status', 'notes'])
            ->withTimestamps();
    }

    /**
     * Cursos que imparte el profesor
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_professor')
            ->withTimestamps();
    }

    /**
     * Cursos que imparte el profesor (alias para compatibilidad)
     */
    public function coursesAsTeacher(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_professor')
            ->withTimestamps();
    }

    /**
     * Sesiones de clase del profesor
     */
    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'professor_id');
    }

    /**
     * Registros de asistencia del estudiante
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Pagos del estudiante
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Progreso en contenidos
     */
    public function contentProgress(): HasMany
    {
        return $this->hasMany(ContentProgress::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeEstudiantes($query)
    {
        return $query->where('role', 'estudiante');
    }

    public function scopeProfesores($query)
    {
        return $query->where('role', 'profesor');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // ==================== PERMISOS ====================

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return RolePermission::hasPermission($this->role, $permissionSlug);
    }

    /**
     * Verificar si el usuario tiene alguno de los permisos dados
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el usuario tiene todos los permisos dados
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Obtener todos los permisos activos del usuario
     */
    public function getPermissions(): array
    {
        if ($this->role === 'admin') {
            return Permission::pluck('slug')->toArray();
        }

        return RolePermission::getPermissionsForRole($this->role);
    }

    // ==================== HELPERS ====================

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo 
            ? asset('storage/' . $this->photo) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getActiveEnrollment()
    {
        return $this->enrollments()->where('status', 'activo')->first();
    }

    public function hasActiveEnrollment(): bool
    {
        return $this->enrollments()->where('status', 'activo')->exists();
    }

    public function getPendingPaymentsCount(): int
    {
        return $this->payments()->where('status', 'pendiente')->count();
    }

    public function getAttendanceRate(): float
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 0;

        $present = $this->attendances()->where('status', 'presente')->count();
        return round(($present / $total) * 100, 1);
    }
}
