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
        Schema::create('temp_release', function (Blueprint $table) {
            $table->bigInteger('temp_rbarcode')->primary();
            $table->integer('temp_rdenom')->index('temp_rdenom');
            $table->dateTime('temp_rdate');
            $table->integer('temp_relno')->index('temp_relno');
            $table->integer('temp_relby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_release');
    }
};
