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
        Schema::create('user_pages', function (Blueprint $table) {
            $table->integer('upages', true);
            $table->integer('upages_pageid');
            $table->integer('upages_userid');
            $table->integer('upages_view');
            $table->integer('upages_update');
            $table->integer('upages_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_pages');
    }
};
