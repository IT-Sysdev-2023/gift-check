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
        Schema::create('gc_type', function (Blueprint $table) {
            $table->integer('gc_type_id', true);
            $table->string('gctype', 20);
            $table->integer('gc_status');
            $table->integer('gc_forallocation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_type');
    }
};
