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
        Schema::create('backup_records', function (Blueprint $table) {
            $table->integer('br_id', true);
            $table->string('br_filename', 100);
            $table->dateTime('br_date');
            $table->integer('br_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_records');
    }
};
