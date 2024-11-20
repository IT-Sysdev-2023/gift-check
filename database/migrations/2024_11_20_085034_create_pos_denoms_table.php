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
        Schema::create('pos_denoms', function (Blueprint $table) {
            $table->integer('pos_did', true);
            $table->decimal('pos_ddenom', 10)->index('pos_ddenom');
            $table->string('pos_ddesc', 50);
            $table->string('pos_dstat', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_denoms');
    }
};
