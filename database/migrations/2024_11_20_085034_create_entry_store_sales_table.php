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
        Schema::create('entry_store_sales', function (Blueprint $table) {
            $table->bigIncrements('ess_id');
            $table->string('ess_type', 5);
            $table->integer('ss_ref_id');
            $table->integer('ess_denom');
            $table->integer('ess_scode');
            $table->integer('ess_pcs');
            $table->integer('ess_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_store_sales');
    }
};
