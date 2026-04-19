<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de programas
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('duration_months')->default(12);
            $table->integer('total_hours')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Tabla de cursos
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_hours')->default(0);
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

        // Tabla de modulos
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_hours')->default(0);
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

        // Tabla de contenidos
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'video', 'audio', 'link', 'text'])->default('text');
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->integer('order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('programs');
    }
};