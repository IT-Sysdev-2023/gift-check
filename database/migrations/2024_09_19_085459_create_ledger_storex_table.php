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
        Schema::create('ledger_storex', function (Blueprint $table) {
            $table->bigInteger('sledger_id', true);
            $table->string('sledger_no', 30);
            $table->string('sledger_type', 5);
            $table->integer('sledger_scode');
            $table->integer('s_pcs');
            $table->integer('s_denom');
            $table->integer('s_debit');
            $table->integer('s_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_storex');
    }
};
