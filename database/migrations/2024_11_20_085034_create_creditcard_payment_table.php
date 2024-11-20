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
        Schema::create('creditcard_payment', function (Blueprint $table) {
            $table->integer('ccpayment_id', true);
            $table->integer('cctrans_transid')->index('cctrans_transid');
            $table->integer('cc_creaditcard')->index('cc_creaditcard');
            $table->string('cc_cardnumber', 40);
            $table->date('cc_cardexpired');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditcard_payment');
    }
};
