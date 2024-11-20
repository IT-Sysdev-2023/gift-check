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
        Schema::create('customer_internal_ar', function (Blueprint $table) {
            $table->integer('ar_id', true);
            $table->integer('ar_cuscode')->index('ar_cuscode');
            $table->dateTime('ar_datetime');
            $table->integer('ar_type');
            $table->unsignedBigInteger('ar_transno');
            $table->integer('ar_trans_id')->index('ar_trans_id');
            $table->decimal('ar_dbamt', 12);
            $table->decimal('ar_cramt', 12);
            $table->decimal('ar_return', 12);
            $table->decimal('ar_adj', 12);
            $table->text('ar_trans_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_internal_ar');
    }
};
