<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old constraint and add new one with 'plin' included
        DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check');
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_method_check CHECK (payment_method IS NULL OR payment_method IN ('efectivo', 'transferencia', 'tarjeta', 'online', 'yape', 'plin'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_method_check');
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_method_check CHECK (payment_method IS NULL OR payment_method IN ('efectivo', 'transferencia', 'tarjeta', 'online', 'yape'))");
    }
};
