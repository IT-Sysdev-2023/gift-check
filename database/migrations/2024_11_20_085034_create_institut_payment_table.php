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
        Schema::create('institut_payment', function (Blueprint $table) {
            $table->integer('insp_id', true);
            $table->unsignedInteger('insp_paymentnum');
            $table->integer('insp_trid')->index('insp_trid');
            $table->string('insp_paymentcustomer', 50)->index('insp_paymentcustomer');
            $table->string('institut_bankname', 100)->nullable();
            $table->string('institut_bankaccountnum', 100)->nullable();
            $table->string('institut_checknumber', 100)->nullable()->index('institut_checknumber');
            $table->decimal('institut_amountrec', 10);
            $table->date('institut_date')->nullable();
            $table->string('institut_jvcustomer', 30)->nullable();
            $table->integer('institut_eodid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institut_payment');
    }
};
