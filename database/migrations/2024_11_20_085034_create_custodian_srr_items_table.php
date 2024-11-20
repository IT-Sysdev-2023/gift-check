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
        Schema::create('custodian_srr_items', function (Blueprint $table) {
            $table->bigInteger('cssitem_barcode')->primary();
            $table->integer('cssitem_recnum')->index('cssitem_recnum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custodian_srr_items');
    }
};
