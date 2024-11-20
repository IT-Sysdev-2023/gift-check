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
        Schema::create('service_charge', function (Blueprint $table) {
            $table->integer('sc_id', true);
            $table->decimal('sc_charge', 10);
            $table->dateTime('sc_datetime');
            $table->integer('sc_store')->index('sc_store');
            $table->integer('sc_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_charge');
    }
};
