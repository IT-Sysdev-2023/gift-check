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
        Schema::create('denomination', function (Blueprint $table) {
            $table->integer('denom_id', true);
            $table->integer('denom_code');
            $table->integer('denomination')->index('denomination');
            $table->unsignedInteger('denom_fad_item_number');
            $table->bigInteger('denom_barcode_start');
            $table->string('denom_type', 5);
            $table->string('denom_status', 10);
            $table->integer('denom_createdby');
            $table->dateTime('denom_datecreated');
            $table->integer('denom_updatedby')->nullable();
            $table->dateTime('denom_dateupdated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denomination');
    }
};
