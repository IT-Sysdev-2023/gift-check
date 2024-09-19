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
        Schema::create('approved_promorequest', function (Blueprint $table) {
            $table->integer('apr_id', true);
            $table->integer('apr_request_id');
            $table->integer('apr_request_relnum');
            $table->string('apr_approvedby', 50);
            $table->string('apr_checkedby', 50);
            $table->text('apr_remarks');
            $table->dateTime('apr_approved_at');
            $table->string('apr_preparedby', 60);
            $table->string('apr_recby', 60);
            $table->string('apr_file_docno');
            $table->integer('apr_stat');
            $table->integer('apr_rec');
            $table->string('apr_paymenttype', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_promorequest');
    }
};
