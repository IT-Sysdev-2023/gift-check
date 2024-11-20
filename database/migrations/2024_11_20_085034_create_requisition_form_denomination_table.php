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
        Schema::create('requisition_form_denomination', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->integer('form_id')->default(0);
            $table->bigInteger('denom_no')->default(0);
            $table->bigInteger('quantity')->default(0);
            $table->char('used', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_form_denomination');
    }
};
