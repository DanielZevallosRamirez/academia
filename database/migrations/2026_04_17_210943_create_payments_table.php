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
        // Pagos
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['efectivo', 'transferencia', 'tarjeta', 'online'])->default('efectivo');
            $table->enum('status', ['pendiente', 'pagado', 'vencido', 'cancelado'])->default('pendiente');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Plan de pagos (cuotas)
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->integer('total_installments')->default(1);
            $table->decimal('installment_amount', 10, 2);
            $table->integer('day_of_month')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Archivos de comprobantes de pago
        Schema::create('payment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_documents');
        Schema::dropIfExists('payment_plans');
        Schema::dropIfExists('payments');
    }
};
