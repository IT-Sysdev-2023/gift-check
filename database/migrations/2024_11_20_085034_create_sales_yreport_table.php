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
        Schema::create('sales_yreport', function (Blueprint $table) {
            $table->integer('yrep_id', true);
            $table->integer('yrep_cashier');
            $table->integer('yrep_supervisor');
            $table->integer('yrep_store');
            $table->dateTime('yrep_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_yreport');
    }
};
