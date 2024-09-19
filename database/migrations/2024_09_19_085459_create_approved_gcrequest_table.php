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
        Schema::create('approved_gcrequest', function (Blueprint $table) {
            $table->integer('agcr_id', true);
            $table->integer('agcr_request_id');
            $table->integer('agcr_request_relnum');
            $table->string('agcr_approvedby', 50);
            $table->string('agcr_checkedby', 50);
            $table->text('agcr_remarks');
            $table->dateTime('agcr_approved_at');
            $table->string('agcr_preparedby', 50);
            $table->string('agcr_recby', 60);
            $table->string('agcr_file_docno', 8);
            $table->integer('agcr_stat');
            $table->integer('agcr_rec')->default(0);
            $table->string('agcr_paymenttype', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_gcrequest');
    }
};
