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
        Schema::create('cancelled_production_request', function (Blueprint $table) {
            $table->integer('cpr_id', true);
            $table->integer('cpr_pro_id');
            $table->integer('cpr_isrequis_cancel');
            $table->integer('cpr_ldgerid')->nullable();
            $table->dateTime('cpr_at');
            $table->integer('cpr_by');
            $table->string('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_production_request');
    }
};
