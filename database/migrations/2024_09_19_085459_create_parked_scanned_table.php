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
        Schema::create('parked_scanned', function (Blueprint $table) {
            $table->integer('ps_id', true);
            $table->bigInteger('ps_scanned');
            $table->dateTime('ps_datetime');
            $table->integer('ps_scannedby');
            $table->integer('ps_module');
            $table->text('ps_errormsg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parked_scanned');
    }
};
