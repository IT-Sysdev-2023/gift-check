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
        Schema::create('transaction_stores', function (Blueprint $table) {
            $table->integer('trans_sid', true);
            $table->unsignedInteger('trans_number')->index('trans_number');
            $table->integer('trans_cashier');
            $table->integer('trans_store')->index('trans_store');
            $table->dateTime('trans_datetime');
            $table->integer('trans_status')->default(0);
            $table->integer('trans_yreport')->default(0);
            $table->integer('trans_type');
            $table->string('trans_eos', 10)->default('');
            $table->string('trans_ip_address', 20)->nullable()->index('trans_ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_stores');
    }
};
