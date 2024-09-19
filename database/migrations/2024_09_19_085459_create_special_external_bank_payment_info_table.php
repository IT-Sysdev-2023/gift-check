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
        Schema::create('special_external_bank_payment_info', function (Blueprint $table) {
            $table->integer('spexgcbi_trid')->primary();
            $table->string('spexgcbi_bankname', 100);
            $table->string('spexgcbi_bankaccountnum', 100);
            $table->string('spexgcbi_checknumber', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_external_bank_payment_info');
    }
};
