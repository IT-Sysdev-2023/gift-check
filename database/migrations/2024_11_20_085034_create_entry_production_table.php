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
        Schema::create('entry_production', function (Blueprint $table) {
            $table->string('ep_title', 5);
            $table->bigIncrements('ep_no');
            $table->date('ep_date');
            $table->time('ep_time');
            $table->string('ep_type', 5);
            $table->integer('ep_amount');
            $table->integer('ep_tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_production');
    }
};
