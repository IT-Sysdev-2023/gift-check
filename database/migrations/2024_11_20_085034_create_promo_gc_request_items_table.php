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
        Schema::create('promo_gc_request_items', function (Blueprint $table) {
            $table->integer('pgcreqi_id', true);
            $table->integer('pgcreqi_trid')->index('pgcreqi_trid');
            $table->integer('pgcreqi_denom');
            $table->integer('pgcreqi_qty');
            $table->integer('pgcreqi_remaining');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_gc_request_items');
    }
};
