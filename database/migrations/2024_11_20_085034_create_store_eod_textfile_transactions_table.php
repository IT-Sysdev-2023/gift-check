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
        Schema::create('store_eod_textfile_transactions', function (Blueprint $table) {
            $table->integer('seodtt_id', true);
            $table->integer('seodtt_eod_id')->index('seodtt_eod_id');
            $table->bigInteger('seodtt_barcode')->index('seodtt_barcode');
            $table->unsignedInteger('seodtt_line')->default(0);
            $table->decimal('seodtt_creditlimit', 10);
            $table->decimal('seodtt_credpuramt', 10);
            $table->decimal('seodtt_addonamt', 10)->default(0);
            $table->decimal('seodtt_balance', 10);
            $table->string('seodtt_transno', 17)->default('0');
            $table->string('seodtt_timetrnx', 6);
            $table->string('seodtt_bu', 10);
            $table->string('seodtt_terminalno', 10);
            $table->integer('seodtt_ackslipno')->default(0);
            $table->decimal('seodtt_crditpurchaseamt', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_eod_textfile_transactions');
    }
};
