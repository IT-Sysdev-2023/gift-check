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
        Schema::create('store_eod', function (Blueprint $table) {
            $table->integer('steod_id', true);
            $table->integer('steod_storeid')->default(0);
            $table->integer('steod_by');
            $table->dateTime('steod_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_eod');
    }
};
