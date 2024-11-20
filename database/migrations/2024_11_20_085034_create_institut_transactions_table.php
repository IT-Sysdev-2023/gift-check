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
        Schema::create('institut_transactions', function (Blueprint $table) {
            $table->integer('institutr_id', true);
            $table->integer('institutr_trnum')->index('institutr_trnum');
            $table->string('institutr_trtype', 20);
            $table->integer('institutr_cusid')->index('institutr_cusid');
            $table->integer('institutr_payfundid');
            $table->string('institutr_paymenttype', 20);
            $table->string('institutr_receivedby', 100);
            $table->string('institutr_checkedby', 100);
            $table->integer('institutr_trby');
            $table->string('institutr_remarks', 100);
            $table->dateTime('institutr_date');
            $table->decimal('institutr_totamtpayable', 12);
            $table->decimal('institutr_checkamt', 12);
            $table->decimal('institutr_cashamt', 12);
            $table->decimal('institutr_amtchange', 12);
            $table->decimal('institutr_totamtrec', 12);
            $table->string('institutr_docname', 50)->nullable();
            $table->string('pay_terms', 30)->default('');
            $table->integer('institutr_eod_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institut_transactions');
    }
};
