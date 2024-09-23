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
        Schema::create('ledger_spgc', function (Blueprint $table) {
            $table->bigInteger('spgcledger_id', true);
            $table->integer('spgcledger_no');
            $table->integer('spgcledger_trid');
            $table->dateTime('spgcledger_datetime');
            $table->string('spgcledger_type', 30);
            $table->integer('spgcledger_typeid')->default(0);
            $table->integer('spgcledger_group')->default(0);
            $table->decimal('spgcledger_debit', 12)->nullable();
            $table->decimal('spgcledger_credit', 12)->nullable();
            $table->integer('spgctag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_spgc');
    }
};
