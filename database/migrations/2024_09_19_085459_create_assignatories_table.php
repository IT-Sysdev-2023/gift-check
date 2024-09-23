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
        Schema::create('assignatories', function (Blueprint $table) {
            $table->integer('assig_id', true);
            $table->integer('assig_dept')->index('assig_dept');
            $table->string('assig_position', 50);
            $table->string('assig_name', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignatories');
    }
};
