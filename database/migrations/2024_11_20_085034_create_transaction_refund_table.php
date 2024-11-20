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
        Schema::create('transaction_refund', function (Blueprint $table) {
            $table->integer('refund_id', true);
            $table->integer('refund_trans_id')->index('refund_trans_id');
            $table->bigInteger('refund_barcode')->index('refund_barcode');
            $table->integer('refund_denom')->index('refund_denom');
            $table->decimal('refund_linedisc', 10);
            $table->decimal('refund_sdisc', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_refund');
    }
};
