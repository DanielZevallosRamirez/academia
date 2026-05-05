<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     */
    public static function setPermission(string $role, int $permissionId, bool $isActive): void
    {
        self::updateOrCreate(
            ['role' => $role, 'permission_id' => $permissionId],
            ['is_active' => $isActive]
        );
    }
}
