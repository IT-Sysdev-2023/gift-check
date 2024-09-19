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
        Schema::create('special_external_customer', function (Blueprint $table) {
            $table->integer('spcus_id', true);
            $table->string('spcus_companyname', 200);
            $table->string('spcus_acctname', 100);
            $table->text('spcus_address');
            $table->string('spcus_cperson', 200);
            $table->string('spcus_cnumber', 40);
            $table->integer('spcus_type');
            $table->dateTime('spcus_at');
            $table->integer('spcus_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_external_customer');
    }
};
