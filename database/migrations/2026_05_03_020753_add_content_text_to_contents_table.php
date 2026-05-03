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
        Schema::table('contents', function (Blueprint $table) {
            if (!Schema::hasColumn('contents', 'content_text')) {
                $table->text('content_text')->nullable()->after('external_url');
            }
            if (!Schema::hasColumn('contents', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('content_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['content_text', 'duration_minutes']);
        });
    }
};
