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
        Schema::create('store_request_items', function (Blueprint $table) {
            $table->integer('sri_id', true);
            $table->integer('sri_items_denomination')->index('sri_items_denomination');
            $table->integer('sri_items_quantity');
            $table->integer('sri_items_remain');
            $table->integer('sri_items_requestid')->index('sri_items_requestid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_request_items');
    }
};
