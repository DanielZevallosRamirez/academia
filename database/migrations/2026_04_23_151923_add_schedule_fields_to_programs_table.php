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
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('programs', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('programs', 'schedule')) {
                $table->string('schedule')->nullable(); // JSON array of schedules
            }
            if (!Schema::hasColumn('programs', 'total_hours')) {
                $table->integer('total_hours')->nullable();
            }
            if (!Schema::hasColumn('programs', 'status')) {
                $table->string('status')->default('activo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'schedule', 'total_hours', 'status']);
        });
    }
};
