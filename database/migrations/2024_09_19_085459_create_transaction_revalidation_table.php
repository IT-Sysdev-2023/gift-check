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
        Schema::create('transaction_revalidation', function (Blueprint $table) {
            $table->integer('reval_id', true);
            $table->integer('reval_trans_id')->index('reval_trans_id');
            $table->bigInteger('reval_barcode')->index('reval_barcode');
            $table->decimal('reval_denom', 11)->index('reval_denom');
            $table->decimal('reval_charge', 10);
            $table->integer('reval_revalidated')->default(0);
            $table->integer('reval_reprint')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_revalidation');
    }
};
