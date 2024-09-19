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
        Schema::create('institut_eod', function (Blueprint $table) {
            $table->integer('ieod_id', true);
            $table->unsignedInteger('ieod_num');
            $table->dateTime('ieod_date');
            $table->integer('ieod_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institut_eod');
    }
};
