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
        Schema::create('production_adjustment', function (Blueprint $table) {
            $table->integer('proadj_id', true);
            $table->integer('proadj_by');
            $table->string('proadj_type', 2);
            $table->dateTime('proadj_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_adjustment');
    }
};
