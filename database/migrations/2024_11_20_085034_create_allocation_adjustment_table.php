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
        Schema::create('allocation_adjustment', function (Blueprint $table) {
            $table->integer('aadj_id', true);
            $table->integer('aadj_loc');
            $table->integer('aadj_gctype');
            $table->integer('aadj_by');
            $table->dateTime('aadj_datetime');
            $table->string('aadj_type', 2);
            $table->string('aadj_remark', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_adjustment');
    }
};
