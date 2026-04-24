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
        // Drop the old constraint
        DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check');
        
        // Add the new constraint with 'yape' included
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_method_check CHECK (payment_method IN ('efectivo', 'transferencia', 'tarjeta', 'online', 'yape'))");
        
        // Also update status constraint to include 'parcial'
        DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check');
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pendiente', 'pagado', 'vencido', 'cancelado', 'parcial'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original constraints
        DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check');
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_method_check CHECK (payment_method IN ('efectivo', 'transferencia', 'tarjeta', 'online'))");
        
        DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check');
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pendiente', 'pagado', 'vencido', 'cancelado'))");
    }
};
