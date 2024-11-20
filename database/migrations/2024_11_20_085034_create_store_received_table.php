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
        Schema::create('store_received', function (Blueprint $table) {
            $table->integer('srec_id', true);
            $table->unsignedInteger('srec_recid')->index('srec_recid');
            $table->integer('srec_rel_id')->index('srec_rel_id');
            $table->integer('srec_store_id')->index('srec_store_id');
            $table->string('srec_receivingtype', 50)->index('srec_receivingtype');
            $table->dateTime('srec_at');
            $table->string('srec_checkedby', 50);
            $table->integer('srec_by');
            $table->unsignedInteger('srec_ledgercheckref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_received');
    }
};
