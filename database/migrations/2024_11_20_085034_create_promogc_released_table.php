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
        Schema::create('promogc_released', function (Blueprint $table) {
            $table->integer('prgcrel_id', true);
            $table->bigInteger('prgcrel_barcode')->index('prgcrel_barcode');
            $table->string('prgcrel_claimant', 60);
            $table->text('prgcrel_address');
            $table->dateTime('prgcrel_at');
            $table->integer('prgcrel_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promogc_released');
    }
};
