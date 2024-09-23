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
        Schema::create('payment_fund', function (Blueprint $table) {
            $table->integer('pay_id', true);
            $table->string('pay_desc', 200);
            $table->string('pay_status', 10);
            $table->dateTime('pay_dateadded');
            $table->integer('pay_addby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_fund');
    }
};
