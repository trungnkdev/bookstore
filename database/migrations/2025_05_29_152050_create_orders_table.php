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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('payment_method')->default('cod'); // cod, card, paypal, bank_transfer
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, failed, refunded
            $table->string('shipping_method')->default('standard'); // standard, express, pickup
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');  // pending, processing, completed, cancelled
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
