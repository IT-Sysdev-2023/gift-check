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
        Schema::create('gc_verification_reprint_details', function (Blueprint $table) {
            $table->integer('gcvrep_id', true);
            $table->bigInteger('gcvrep_barcode')->index('gcvrep_barcode');
            $table->dateTime('gcvrep_datetime');
            $table->integer('gcvrep_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_verification_reprint_details');
    }
};
