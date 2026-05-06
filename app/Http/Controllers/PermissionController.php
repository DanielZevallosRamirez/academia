<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Mostrar la página de gestión de permisos
     */
    public function index()
    {
        $permissions = Permission::orderBy('module')->orderBy('order')->get();
        $groupedPermissions = $permissions->groupBy('module');
        
        $roles = array_keys(User::ROLES);
        
        // Obtener estado de permisos para cada rol (colección completa con is_active)
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role] = RolePermission::where('role', $role)->get();
        }
        
        // Total de permisos
        $totalPermissions = $permissions->count();
        
        // Contar permisos activos por rol
        $roleActiveCounts = [];
        foreach ($roles as $role) {
            $roleActiveCounts[$role] = $rolePermissions[$role]->where('is_active', true)->count();
        }
        
        // Nombres legibles de módulos
        $moduleNames = [
            'dashboard' => 'Dashboard',
            'programs' => 'Programas',
            'students' => 'Estudiantes',
            'users' => 'Usuarios',
            'professors' => 'Profesores',
            'payments' => 'Pagos',
            'attendance' => 'Asistencia',
            'sessions' => 'Sesiones de Clase',
            'reports' => 'Reportes',
            'notifications' => 'Notificaciones',
            'settings' => 'Configuracion',
            'portal' => 'Portal Estudiante',
        ];
        
        // Nombres legibles de roles
        $roleNames = User::ROLES;

        return view('permissions.index', compact(
            'permissions',
            'groupedPermissions',
            'roles',
            'rolePermissions',
            'roleActiveCounts',
            'totalPermissions',
            'moduleNames',
            'roleNames'
        ));
    }

    /**
     * Actualizar permisos de un rol
     */
    public function update(Request $request)
    {
        $role = $request->input('role');
        $permissions = $request->input('permissions', []);
        
        if (!in_array($role, ['admin', 'profesor', 'estudiante'])) {
            return back()->with('error', 'Rol no valido.');
        }

        // Obtener todos los permisos
        $allPermissions = Permission::all();
        
        foreach ($allPermissions as $permission) {
            $isActive = in_array($permission->id, $permissions);
            RolePermission::setPermission($role, $permission->id, $isActive);
        }

        // Notificar
        $roleNames = [
            'admin' => 'Administrador',
            'profesor' => 'Profesor',
            'estudiante' => 'Estudiante',
        ];

        Notification::notifyAdmins(
            Notification::TYPE_SYSTEM,
            'Permisos actualizados',
            "Se han actualizado los permisos del rol {$roleNames[$role]}.",
            route('permissions.index'),
            ['role' => $role]
        );

        return back()->with('success', "Permisos del rol {$roleNames[$role]} actualizados correctamente.");
    }

    /**
     * Actualizar un permiso individual vía AJAX
     */
    public function toggle(Request $request)
    {
        $role = $request->input('role');
        $permissionId = $request->input('permission_id');
        $isActive = $request->boolean('is_active');

        if (!array_key_exists($role, User::ROLES)) {
            return response()->json(['success' => false, 'message' => 'Rol no valido'], 400);
        }

        RolePermission::setPermission($role, $permissionId, $isActive);

        // Obtener nombre del permiso para la notificación
        $permission = Permission::find($permissionId);

        // Crear notificación
        $action = $isActive ? 'activado' : 'desactivado';
        Notification::notifyAdmins(
            Notification::TYPE_SYSTEM,
            'Permiso ' . $action,
            "Se ha {$action} el permiso '{$permission->name}' para el rol " . User::getRoleName($role) . ".",
            route('permissions.index'),
            ['role' => $role, 'permission' => $permission->slug, 'action' => $action]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Restablecer permisos por defecto de un rol
     */
    public function reset(Request $request)
    {
        $role = $request->input('role');
        
        if (!in_array($role, ['admin', 'profesor', 'estudiante'])) {
            return back()->with('error', 'Rol no valido.');
        }

        $defaultSlugs = Permission::getDefaultPermissionsForRole($role);
        $allPermissions = Permission::all();
        
        foreach ($allPermissions as $permission) {
            $isActive = in_array($permission->slug, $defaultSlugs);
            RolePermission::setPermission($role, $permission->id, $isActive);
        }

        $roleNames = [
            'admin' => 'Administrador',
            'profesor' => 'Profesor',
            'estudiante' => 'Estudiante',
        ];

        return back()->with('success', "Permisos del rol {$roleNames[$role]} restablecidos a valores por defecto.");
    }
}
