<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\RolePermission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Directiva @can para permisos personalizados
        Blade::if('permission', function (string $permission) {
            $user = auth()->user();
            
            if (!$user) {
                return false;
            }
            
            // Admin siempre tiene todos los permisos
            if ($user->role === 'admin') {
                return true;
            }
            
            return RolePermission::hasPermission($user->role, $permission);
        });

        // Directiva @anyPermission para verificar si tiene alguno de los permisos
        Blade::if('anyPermission', function (array $permissions) {
            $user = auth()->user();
            
            if (!$user) {
                return false;
            }
            
            if ($user->role === 'admin') {
                return true;
            }
            
            foreach ($permissions as $permission) {
                if (RolePermission::hasPermission($user->role, $permission)) {
                    return true;
                }
            }
            
            return false;
        });
    }
}
