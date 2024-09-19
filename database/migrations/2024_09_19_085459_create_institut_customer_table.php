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
        Schema::create('institut_customer', function (Blueprint $table) {
            $table->integer('ins_id', true);
            $table->string('ins_name', 50);
            $table->string('ins_status', 10);
            $table->string('ins_custype', 20);
            $table->integer('ins_gctype')->default(0);
            $table->dateTime('ins_date_created');
            $table->integer('ins_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institut_customer');
    }
};
