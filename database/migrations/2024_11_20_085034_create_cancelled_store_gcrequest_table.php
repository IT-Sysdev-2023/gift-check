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
        Schema::create('cancelled_store_gcrequest', function (Blueprint $table) {
            $table->integer('csgr_id', true);
            $table->integer('csgr_gc_id');
            $table->dateTime('csgr_at');
            $table->integer('csgr_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_store_gcrequest');
    }
};
