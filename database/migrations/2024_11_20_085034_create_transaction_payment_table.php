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
        Schema::create('transaction_payment', function (Blueprint $table) {
            $table->integer('payment_id', true);
            $table->integer('payment_trans_num')->index('payment_trans_num');
            $table->string('payment_receipt_no', 40)->nullable()->default('');
            $table->integer('payment_items');
            $table->decimal('payment_stotal', 12);
            $table->decimal('payment_amountdue', 10);
            $table->decimal('payment_cash', 10);
            $table->decimal('payment_change', 10);
            $table->decimal('payment_docdisc', 12)->default(0);
            $table->decimal('payment_linediscount', 12)->default(0);
            $table->decimal('payment_internal_discount', 12)->default(0);
            $table->string('payment_tender', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_payment');
    }
};
