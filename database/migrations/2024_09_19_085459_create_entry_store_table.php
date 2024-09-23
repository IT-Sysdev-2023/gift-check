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
        Schema::create('entry_store', function (Blueprint $table) {
            $table->bigIncrements('es_no');
            $table->string('es_type', 3);
            $table->integer('es_denom');
            $table->integer('es_qty');
            $table->integer('es_scode');
            $table->integer('es_amt');
            $table->date('es_date');
            $table->time('es_time');
            $table->integer('es_by');
            $table->string('es_tag', 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_store');
    }
};
