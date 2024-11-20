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
        Schema::create('transfer_receiving', function (Blueprint $table) {
            $table->integer('trans_recid')->primary();
            $table->unsignedInteger('trans_recnum')->index('trans_recnum');
            $table->integer('trans_recrelid')->index('trans_recrelid');
            $table->string('trans_recremarks', 100);
            $table->string('trans_reccheckedby', 50);
            $table->dateTime('trans_recdate');
            $table->integer('trans_recby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_receiving');
    }
};
