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
        Schema::create('promo_gc_tag', function (Blueprint $table) {
            $table->integer('promotagID', true);
            $table->string('promotagDesc', 200);
            $table->integer('promotagCreatedBy');
            $table->dateTime('promotagCreatedAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_gc_tag');
    }
};
