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
        Schema::create('envelope_trans', function (Blueprint $table) {
            $table->integer('env_trans_id', true);
            $table->integer('env_cashier_store_assign');
            $table->integer('env_trans_pcs');
            $table->double('env_trans_tot_amount');
            $table->dateTime('env_trans_date');
            $table->double('env_trans_amt_tender');
            $table->integer('env_trans_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envelope_trans');
    }
};
