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
        Schema::create('promo', function (Blueprint $table) {
            $table->integer('promo_id', true);
            $table->unsignedInteger('promo_num')->index('promo_num');
            $table->string('promo_name', 100);
            $table->integer('promo_group');
            $table->integer('promo_tag');
            $table->dateTime('promo_date');
            $table->text('promo_remarks');
            $table->date('promo_dateexpire');
            $table->date('promo_datenotified');
            $table->date('promo_drawdate');
            $table->integer('promo_valby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo');
    }
};
