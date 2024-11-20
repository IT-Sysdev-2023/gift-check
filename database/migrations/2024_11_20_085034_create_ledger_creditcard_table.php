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
        Schema::create('ledger_creditcard', function (Blueprint $table) {
            $table->integer('ccled_id', true);
            $table->integer('ccled_transid');
            $table->string('ccled_transtype', 5);
            $table->integer('ccled_creditcardid');
            $table->decimal('ccled_debit_amt', 10);
            $table->decimal('ccled_credit_amt', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_creditcard');
    }
};
