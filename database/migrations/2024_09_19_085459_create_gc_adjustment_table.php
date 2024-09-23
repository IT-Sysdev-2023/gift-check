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
        Schema::create('gc_adjustment', function (Blueprint $table) {
            $table->integer('gc_adj_id', true);
            $table->string('gc_adj_type', 5);
            $table->string('gc_adj_remarks', 100);
            $table->dateTime('gc_adj_datetime');
            $table->integer('gc_adj_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_adjustment');
    }
};
