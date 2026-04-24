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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('payments', 'concept')) {
                $table->string('concept')->nullable()->after('invoice_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
            if (Schema::hasColumn('payments', 'concept')) {
                $table->dropColumn('concept');
            }
        });
    }
};
