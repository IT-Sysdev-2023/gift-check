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
        Schema::create('entry_check_request', function (Blueprint $table) {
            $table->string('cr_title', 5);
            $table->bigInteger('cr_no', true);
            $table->date('cr_date');
            $table->time('cr_time');
            $table->string('cr_type', 5);
            $table->integer('cr_denomination')->index('cr_denomination');
            $table->integer('cr_qty');
            $table->integer('cr_amount');
            $table->integer('cr_tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_check_request');
    }
};
