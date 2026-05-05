<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de permisos disponibles
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre legible: "Ver Programas"
            $table->string('slug')->unique(); // Identificador: "programs.view"
            $table->string('module'); // Módulo: "programs", "students", "payments"
            $table->string('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Tabla pivot para permisos por rol
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // admin, profesor, estudiante
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['role', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
