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
        Schema::create('transaction_refund_details', function (Blueprint $table) {
            $table->integer('trefundd_id', true);
            $table->integer('trefundd_trstoresid')->index('trefundd_trstoresid');
            $table->integer('trefundd_trid_refund');
            $table->decimal('trefundd_totgcrefund', 10);
            $table->decimal('trefundd_total_linedisc', 10);
            $table->decimal('trefundd_subtotal_disc', 10);
            $table->decimal('trefundd_servicecharge', 10);
            $table->decimal('trefundd_refundamt', 10);

            $table->index(['trefundd_trstoresid'], 'trefundd_trstoresid_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_refund_details');
    }
};
