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
        Schema::create('lost_gc_details', function (Blueprint $table) {
            $table->integer('lostgcd_id', true);
            $table->unsignedInteger('lostgcd_repnum');
            $table->integer('lostgcd_storeid')->index('lostgcd_storeid');
            $table->string('lostgcd_owname', 50);
            $table->text('lostgcd_address');
            $table->string('lostgcd_contactnum', 15);
            $table->dateTime('lostgcd_datereported');
            $table->date('lostgcd_datelost');
            $table->integer('lostgcd_prepby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_gc_details');
    }
};
