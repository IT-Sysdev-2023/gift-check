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
        Schema::create('manual_setgc', function (Blueprint $table) {
            $table->integer('mgc_id', true);
            $table->integer('mgc_manualnum')->nullable();
            $table->string('mgc_manualtype', 60);
            $table->string('mgc_note', 50);
            $table->integer('mgc_by');
            $table->dateTime('mgc_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_setgc');
    }
};
