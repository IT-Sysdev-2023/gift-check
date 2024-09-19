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
        Schema::create('beamandgo_barcodes', function (Blueprint $table) {
            $table->integer('bngbar_id', true);
            $table->bigInteger('bngbar_barcode');
            $table->integer('bngbar_trid');
            $table->string('bngbar_serialnum', 20);
            $table->string('bngbar_refnum', 25);
            $table->string('bngbar_transdate', 30);
            $table->string('bngbar_sendername', 50);
            $table->string('bngbar_beneficiaryname', 50);
            $table->string('bngbar_beneficiarymobile', 15);
            $table->decimal('bngbar_value', 10, 0);
            $table->string('bngbar_branchname', 50);
            $table->string('bngbar_status', 15)->nullable();
            $table->string('bngbar_note', 100)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beamandgo_barcodes');
    }
};
