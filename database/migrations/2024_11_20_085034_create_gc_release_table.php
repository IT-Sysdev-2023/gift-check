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
        Schema::create('gc_release', function (Blueprint $table) {
            $table->integer('rel_id', true);
            $table->bigInteger('re_barcode_no')->index('re_barcode_no');
            $table->integer('rel_storegcreq_id');
            $table->integer('rel_store_id')->index('rel_store_id');
            $table->integer('rel_num')->index('rel_num');
            $table->dateTime('rel_date');
            $table->integer('rel_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_release');
    }
};
