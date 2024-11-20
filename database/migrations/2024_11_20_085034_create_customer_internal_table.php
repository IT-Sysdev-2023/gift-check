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
        Schema::create('customer_internal', function (Blueprint $table) {
            $table->integer('ci_code')->primary();
            $table->string('ci_name', 50);
            $table->integer('ci_group');
            $table->integer('ci_type');
            $table->text('ci_address');
            $table->string('ci_cstatus', 20);
            $table->dateTime('ci_datecreated');
            $table->integer('ci_createdby');
            $table->integer('ci_distype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_internal');
    }
};
