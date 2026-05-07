<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\RolePermission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear todos los permisos del sistema
        $permissions = Permission::getSystemPermissions();
        
        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        // Asignar permisos por defecto a cada rol
        $roles = array_keys(\App\Models\User::ROLES);
        
        foreach ($roles as $role) {
            $defaultSlugs = Permission::getDefaultPermissionsForRole($role);
            $allPermissions = Permission::all();
            
            foreach ($allPermissions as $permission) {
                $isActive = in_array($permission->slug, $defaultSlugs);
                
                // Usar DB::statement para PostgreSQL con cast explícito
                $existing = RolePermission::where('role', $role)
                    ->where('permission_id', $permission->id)
                    ->first();
                
                if ($existing) {
                    DB::statement(
                        'UPDATE role_permissions SET is_active = ?::boolean, updated_at = NOW() WHERE role = ? AND permission_id = ?',
                        [$isActive ? 'true' : 'false', $role, $permission->id]
                    );
                } else {
                    DB::statement(
                        'INSERT INTO role_permissions (role, permission_id, is_active, created_at, updated_at) VALUES (?, ?, ?::boolean, NOW(), NOW())',
                        [$role, $permission->id, $isActive ? 'true' : 'false']
                    );
                }
            }
        }
    }
}
