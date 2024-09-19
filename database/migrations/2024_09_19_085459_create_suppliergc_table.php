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
        Schema::create('suppliergc', function (Blueprint $table) {
            $table->integer('suppgc_id')->primary();
            $table->string('suppgc_compname', 100);
            $table->dateTime('suppgc_datecreated');
            $table->integer('suppgc_createdby');
            $table->dateTime('suppgc_dateupdated')->nullable();
            $table->integer('suppgc_updatedby')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliergc');
    }
};
