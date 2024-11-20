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
        Schema::create('beamandgo_transaction', function (Blueprint $table) {
            $table->integer('bngver_id', true);
            $table->integer('bngver_storeid');
            $table->string('bngver_trnum', 20);
            $table->decimal('bngver_amt', 12)->nullable();
            $table->dateTime('bngver_datetime');
            $table->integer('bngver_by');
            $table->integer('bngver_eod')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beamandgo_transaction');
    }
};
