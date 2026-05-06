<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
                RolePermission::updateOrCreate(
                    ['role' => $role, 'permission_id' => $permission->id],
                    ['is_active' => in_array($permission->slug, $defaultSlugs)]
                );
            }
        }
    }
}
