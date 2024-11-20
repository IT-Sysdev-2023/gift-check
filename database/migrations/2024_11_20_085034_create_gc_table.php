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
        Schema::create('gc', function (Blueprint $table) {
            $table->bigInteger('gc_id', true);
            $table->bigInteger('barcode_no')->index('barcode_no');
            $table->integer('denom_id')->index('denom_id');
            $table->date('date');
            $table->time('time');
            $table->integer('pe_entry_gc');
            $table->integer('tag')->default(0);
            $table->integer('gc_postedby');
            $table->string('gc_validated', 3)->default('');
            $table->string('gc_allocated', 3)->default('');
            $table->string('gc_released', 3)->default('');
            $table->string('gc_cancelled', 3)->default('');
            $table->string('gc_ispromo', 3)->default('');
            $table->string('gc_treasury_release', 3)->default('');
            $table->string('status', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc');
    }
};
