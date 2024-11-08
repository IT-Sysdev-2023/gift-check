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
        Schema::create('validation_corp', function (Blueprint $table) {
            $table->integer('vc_id', true);
            $table->bigInteger('vc_barcode_no');
            $table->date('vc_date');
            $table->time('vc_time');
            $table->integer('vc_validatedby');
            $table->string('vc_allocated', 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_corp');
    }
};
