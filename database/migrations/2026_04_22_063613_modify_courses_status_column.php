<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si la columna is_active existe
        if (Schema::hasColumn('courses', 'is_active')) {
            // Agregar columna status
            Schema::table('courses', function (Blueprint $table) {
                $table->string('status')->default('activo')->after('order');
            });

            // Migrar datos de is_active a status
            DB::table('courses')->where('is_active', true)->update(['status' => 'activo']);
            DB::table('courses')->where('is_active', false)->update(['status' => 'inactivo']);

            // Eliminar columna is_active
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'status')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('order');
            });

            DB::table('courses')->where('status', 'activo')->update(['is_active' => true]);
            DB::table('courses')->where('status', 'inactivo')->update(['is_active' => false]);

            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
