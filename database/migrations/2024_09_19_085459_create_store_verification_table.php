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
        Schema::create('store_verification', function (Blueprint $table) {
            $table->integer('vs_id', true);
            $table->bigInteger('vs_barcode')->index('vs_barcode');
            $table->integer('vs_cn');
            $table->integer('vs_store')->index('vs_store');
            $table->integer('vs_by');
            $table->date('vs_date');
            $table->time('vs_time');
            $table->dateTime('vs_reverifydate')->nullable();
            $table->integer('vs_reverifyby')->nullable();
            $table->string('vs_tf', 60);
            $table->string('vs_tf_used', 3)->default('');
            $table->decimal('vs_tf_denomination', 12)->index('vs_tf_denomination');
            $table->decimal('vs_tf_balance', 10);
            $table->decimal('vs_tf_purchasecredit', 10)->default(0);
            $table->decimal('vs_tf_addon_amt', 10)->default(0);
            $table->string('vs_tf_eod', 3)->default('');
            $table->string('vs_tf_eod2', 3)->default('');
            $table->integer('vs_gctype');
            $table->string('vs_trans_manualid', 5)->nullable();
            $table->string('vs_payto', 30);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_verification');
    }
};
