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
        Schema::create('temp_reval', function (Blueprint $table) {
            $table->integer('treval_id', true);
            $table->bigInteger('treval_barcode')->index('treval_barcode');
            $table->integer('treval_by')->index('treval_by');
            $table->integer('treval_store')->index('treval_store');
            $table->decimal('treval_charge', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_reval');
    }
};
