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
        Schema::create('gcbarcodegenerate', function (Blueprint $table) {
            $table->integer('gbcg_id', true);
            $table->integer('gbcg_pro_id');
            $table->integer('gbcg_by')->index('gbcg_by');
            $table->dateTime('gbcg_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gcbarcodegenerate');
    }
};
