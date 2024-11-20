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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->integer('ccard_id', true);
            $table->string('ccard_name', 50);
            $table->string('ccard_status', 20);
            $table->dateTime('ccard_created');
            $table->dateTime('ccard_modified')->nullable();
            $table->integer('ccard_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
