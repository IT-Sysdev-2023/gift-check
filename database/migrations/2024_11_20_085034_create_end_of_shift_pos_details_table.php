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
        Schema::create('end_of_shift_pos_details', function (Blueprint $table) {
            $table->integer('eos_id', true);
            $table->dateTime('eosdatetime');
            $table->integer('eoscashier');
            $table->integer('eosmanager');
            $table->integer('eosstore')->index('eosstore');
            $table->integer('eostrans_id_start');
            $table->integer('eostrans_id_end');
            $table->decimal('eostrans_shtagepveragetotal', 12)->nullable();
            $table->dateTime('eostrans_sht_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('end_of_shift_pos_details');
    }
};
