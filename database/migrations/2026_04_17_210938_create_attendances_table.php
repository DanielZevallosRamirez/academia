<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sesiones de clase (para marcar asistencia)
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->enum('status', ['programada', 'en_curso', 'finalizada', 'cancelada'])->default('programada');
            $table->timestamps();
        });

        // Registro de asistencia
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['presente', 'ausente', 'tardanza', 'justificado'])->default('ausente');
            $table->timestamp('check_in_time')->nullable();
            $table->string('check_in_method')->default('qr'); // qr, manual
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['class_session_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('class_sessions');
    }
};
