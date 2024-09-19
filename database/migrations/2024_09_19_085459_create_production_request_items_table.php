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
        Schema::create('production_request_items', function (Blueprint $table) {
            $table->integer('pe_items_id', true);
            $table->integer('pe_items_denomination');
            $table->integer('pe_items_quantity');
            $table->integer('pe_items_remain');
            $table->integer('pe_items_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_request_items');
    }
};
