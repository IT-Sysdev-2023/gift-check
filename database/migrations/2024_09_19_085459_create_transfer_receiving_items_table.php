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
        Schema::create('transfer_receiving_items', function (Blueprint $table) {
            $table->integer('trans_reciid', true);
            $table->bigInteger('trans_recibarcode')->index('trans_recibarcode');
            $table->integer('trans_recitrid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_receiving_items');
    }
};
