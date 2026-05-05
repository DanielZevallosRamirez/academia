<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
        'order',
    ];

    /**
     * Obtener los permisos de rol para este permiso
     */
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    /**
     * Verificar si un rol tiene este permiso activo
     */
    public function isActiveForRole(string $role): bool
    {
        $rolePermission = $this->rolePermissions()->where('role', $role)->first();
        return $rolePermission ? $rolePermission->is_active : false;
    }

    /**
     * Obtener todos los permisos agrupados por módulo
     */
    public static function getGroupedByModule()
    {
        return self::orderBy('module')->orderBy('order')->get()->groupBy('module');
    }

    /**
     * Lista de todos los permisos del sistema con sus definiciones
     */
    public static function getSystemPermissions(): array
    {
        return [
            // Módulo Dashboard
            [
                'name' => 'Ver Dashboard',
                'slug' => 'dashboard.view',
                'module' => 'dashboard',
                'description' => 'Acceder al panel principal',
                'order' => 1,
            ],
            [
                'name' => 'Ver Estadisticas',
                'slug' => 'dashboard.stats',
                'module' => 'dashboard',
                'description' => 'Ver estadisticas y metricas del sistema',
                'order' => 2,
            ],

            // Módulo Programas
            [
                'name' => 'Ver Programas',
                'slug' => 'programs.view',
                'module' => 'programs',
                'description' => 'Ver lista de programas',
                'order' => 1,
            ],
            [
                'name' => 'Crear Programas',
                'slug' => 'programs.create',
                'module' => 'programs',
                'description' => 'Crear nuevos programas',
                'order' => 2,
            ],
            [
                'name' => 'Editar Programas',
                'slug' => 'programs.edit',
                'module' => 'programs',
                'description' => 'Modificar programas existentes',
                'order' => 3,
            ],
            [
                'name' => 'Eliminar Programas',
                'slug' => 'programs.delete',
                'module' => 'programs',
                'description' => 'Eliminar programas',
                'order' => 4,
            ],
            [
                'name' => 'Gestionar Cursos',
                'slug' => 'programs.courses',
                'module' => 'programs',
                'description' => 'Agregar, editar y eliminar cursos',
                'order' => 5,
            ],
            [
                'name' => 'Gestionar Modulos',
                'slug' => 'programs.modules',
                'module' => 'programs',
                'description' => 'Agregar, editar y eliminar modulos',
                'order' => 6,
            ],
            [
                'name' => 'Gestionar Contenidos',
                'slug' => 'programs.contents',
                'module' => 'programs',
                'description' => 'Agregar, editar y eliminar contenidos',
                'order' => 7,
            ],

            // Módulo Estudiantes
            [
                'name' => 'Ver Estudiantes',
                'slug' => 'students.view',
                'module' => 'students',
                'description' => 'Ver lista de estudiantes',
                'order' => 1,
            ],
            [
                'name' => 'Crear Estudiantes',
                'slug' => 'students.create',
                'module' => 'students',
                'description' => 'Registrar nuevos estudiantes',
                'order' => 2,
            ],
            [
                'name' => 'Editar Estudiantes',
                'slug' => 'students.edit',
                'module' => 'students',
                'description' => 'Modificar datos de estudiantes',
                'order' => 3,
            ],
            [
                'name' => 'Eliminar Estudiantes',
                'slug' => 'students.delete',
                'module' => 'students',
                'description' => 'Eliminar estudiantes',
                'order' => 4,
            ],
            [
                'name' => 'Matricular Estudiantes',
                'slug' => 'students.enroll',
                'module' => 'students',
                'description' => 'Matricular estudiantes en programas',
                'order' => 5,
            ],

            // Módulo Profesores
            [
                'name' => 'Ver Profesores',
                'slug' => 'professors.view',
                'module' => 'professors',
                'description' => 'Ver lista de profesores',
                'order' => 1,
            ],
            [
                'name' => 'Crear Profesores',
                'slug' => 'professors.create',
                'module' => 'professors',
                'description' => 'Registrar nuevos profesores',
                'order' => 2,
            ],
            [
                'name' => 'Editar Profesores',
                'slug' => 'professors.edit',
                'module' => 'professors',
                'description' => 'Modificar datos de profesores',
                'order' => 3,
            ],
            [
                'name' => 'Eliminar Profesores',
                'slug' => 'professors.delete',
                'module' => 'professors',
                'description' => 'Eliminar profesores',
                'order' => 4,
            ],

            // Módulo Pagos
            [
                'name' => 'Ver Pagos',
                'slug' => 'payments.view',
                'module' => 'payments',
                'description' => 'Ver lista de pagos',
                'order' => 1,
            ],
            [
                'name' => 'Registrar Pagos',
                'slug' => 'payments.create',
                'module' => 'payments',
                'description' => 'Registrar nuevos pagos',
                'order' => 2,
            ],
            [
                'name' => 'Aprobar/Rechazar Pagos',
                'slug' => 'payments.approve',
                'module' => 'payments',
                'description' => 'Aprobar o rechazar pagos pendientes',
                'order' => 3,
            ],
            [
                'name' => 'Eliminar Pagos',
                'slug' => 'payments.delete',
                'module' => 'payments',
                'description' => 'Eliminar registros de pagos',
                'order' => 4,
            ],
            [
                'name' => 'Ver Mis Pagos',
                'slug' => 'payments.own',
                'module' => 'payments',
                'description' => 'Ver pagos propios (estudiantes)',
                'order' => 5,
            ],

            // Módulo Asistencia
            [
                'name' => 'Ver Asistencia',
                'slug' => 'attendance.view',
                'module' => 'attendance',
                'description' => 'Ver registros de asistencia',
                'order' => 1,
            ],
            [
                'name' => 'Registrar Asistencia',
                'slug' => 'attendance.create',
                'module' => 'attendance',
                'description' => 'Registrar asistencia de estudiantes',
                'order' => 2,
            ],
            [
                'name' => 'Escanear QR',
                'slug' => 'attendance.scan',
                'module' => 'attendance',
                'description' => 'Escanear codigo QR para asistencia',
                'order' => 3,
            ],
            [
                'name' => 'Ver Mi Asistencia',
                'slug' => 'attendance.own',
                'module' => 'attendance',
                'description' => 'Ver asistencia propia (estudiantes)',
                'order' => 4,
            ],

            // Módulo Sesiones de Clase
            [
                'name' => 'Ver Sesiones',
                'slug' => 'sessions.view',
                'module' => 'sessions',
                'description' => 'Ver sesiones de clase',
                'order' => 1,
            ],
            [
                'name' => 'Crear Sesiones',
                'slug' => 'sessions.create',
                'module' => 'sessions',
                'description' => 'Crear sesiones de clase',
                'order' => 2,
            ],
            [
                'name' => 'Editar Sesiones',
                'slug' => 'sessions.edit',
                'module' => 'sessions',
                'description' => 'Modificar sesiones de clase',
                'order' => 3,
            ],
            [
                'name' => 'Eliminar Sesiones',
                'slug' => 'sessions.delete',
                'module' => 'sessions',
                'description' => 'Eliminar sesiones de clase',
                'order' => 4,
            ],

            // Módulo Reportes
            [
                'name' => 'Ver Reportes',
                'slug' => 'reports.view',
                'module' => 'reports',
                'description' => 'Acceder a reportes del sistema',
                'order' => 1,
            ],
            [
                'name' => 'Exportar Reportes',
                'slug' => 'reports.export',
                'module' => 'reports',
                'description' => 'Exportar reportes a Excel/PDF',
                'order' => 2,
            ],

            // Módulo Notificaciones
            [
                'name' => 'Ver Notificaciones',
                'slug' => 'notifications.view',
                'module' => 'notifications',
                'description' => 'Ver notificaciones del sistema',
                'order' => 1,
            ],
            [
                'name' => 'Gestionar Notificaciones',
                'slug' => 'notifications.manage',
                'module' => 'notifications',
                'description' => 'Marcar como leido, eliminar notificaciones',
                'order' => 2,
            ],

            // Módulo Configuración
            [
                'name' => 'Ver Configuracion',
                'slug' => 'settings.view',
                'module' => 'settings',
                'description' => 'Ver configuracion del sistema',
                'order' => 1,
            ],
            [
                'name' => 'Editar Configuracion',
                'slug' => 'settings.edit',
                'module' => 'settings',
                'description' => 'Modificar configuracion del sistema',
                'order' => 2,
            ],
            [
                'name' => 'Gestionar Permisos',
                'slug' => 'settings.permissions',
                'module' => 'settings',
                'description' => 'Administrar permisos de roles',
                'order' => 3,
            ],

            // Módulo Portal Estudiante
            [
                'name' => 'Acceder Portal Estudiante',
                'slug' => 'portal.access',
                'module' => 'portal',
                'description' => 'Acceder al portal de estudiante',
                'order' => 1,
            ],
            [
                'name' => 'Ver Mi Programa',
                'slug' => 'portal.program',
                'module' => 'portal',
                'description' => 'Ver programa matriculado',
                'order' => 2,
            ],
            [
                'name' => 'Ver Contenidos',
                'slug' => 'portal.contents',
                'module' => 'portal',
                'description' => 'Ver contenidos del programa',
                'order' => 3,
            ],
            [
                'name' => 'Ver Mi QR',
                'slug' => 'portal.qr',
                'module' => 'portal',
                'description' => 'Ver codigo QR personal',
                'order' => 4,
            ],
        ];
    }

    /**
     * Permisos por defecto para cada rol
     */
    public static function getDefaultPermissionsForRole(string $role): array
    {
        $defaults = [
            'admin' => [
                // Admin tiene todos los permisos
                'dashboard.view', 'dashboard.stats',
                'programs.view', 'programs.create', 'programs.edit', 'programs.delete',
                'programs.courses', 'programs.modules', 'programs.contents',
                'students.view', 'students.create', 'students.edit', 'students.delete', 'students.enroll',
                'professors.view', 'professors.create', 'professors.edit', 'professors.delete',
                'payments.view', 'payments.create', 'payments.approve', 'payments.delete',
                'attendance.view', 'attendance.create', 'attendance.scan',
                'sessions.view', 'sessions.create', 'sessions.edit', 'sessions.delete',
                'reports.view', 'reports.export',
                'notifications.view', 'notifications.manage',
                'settings.view', 'settings.edit', 'settings.permissions',
            ],
            'profesor' => [
                'dashboard.view',
                'programs.view',
                'students.view',
                'attendance.view', 'attendance.create', 'attendance.scan',
                'sessions.view',
                'notifications.view',
            ],
            'estudiante' => [
                'portal.access', 'portal.program', 'portal.contents', 'portal.qr',
                'payments.own',
                'attendance.own',
                'notifications.view',
            ],
        ];

        return $defaults[$role] ?? [];
    }
}
