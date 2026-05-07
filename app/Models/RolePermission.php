<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'permission_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtener el permiso asociado
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Verificar si un rol tiene un permiso específico
     */
    public static function hasPermission(string $role, string $permissionSlug): bool
    {
        $permission = Permission::where('slug', $permissionSlug)->first();
        
        if (!$permission) {
            return false;
        }

        $rolePermission = self::where('role', $role)
            ->where('permission_id', $permission->id)
            ->first();

        return $rolePermission ? $rolePermission->is_active : false;
    }

    /**
     * Obtener todos los permisos de un rol
     */
    public static function getPermissionsForRole(string $role): array
    {
        return self::where('role', $role)
            ->where('is_active', true)
            ->with('permission')
            ->get()
            ->pluck('permission.slug')
            ->toArray();
    }

    /**
     * Activar o desactivar un permiso para un rol
     * Usa DB::statement con cast explícito para PostgreSQL
     */
    public static function setPermission(string $role, int $permissionId, bool $isActive): void
    {
        $existing = self::where('role', $role)
            ->where('permission_id', $permissionId)
            ->first();
        
        $boolValue = $isActive ? 'true' : 'false';
        
        if ($existing) {
            DB::statement(
                'UPDATE role_permissions SET is_active = ?::boolean, updated_at = NOW() WHERE role = ? AND permission_id = ?',
                [$boolValue, $role, $permissionId]
            );
        } else {
            DB::statement(
                'INSERT INTO role_permissions (role, permission_id, is_active, created_at, updated_at) VALUES (?, ?, ?::boolean, NOW(), NOW())',
                [$role, $permissionId, $boolValue]
            );
        }
    }
}
