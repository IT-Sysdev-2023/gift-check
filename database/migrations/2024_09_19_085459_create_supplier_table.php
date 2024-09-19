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
        Schema::create('supplier', function (Blueprint $table) {
            $table->integer('gcs_id', true);
            $table->string('gcs_companyname', 100);
            $table->string('gcs_accountname', 100);
            $table->string('gcs_contactperson', 100);
            $table->string('gcs_contactnumber', 30);
            $table->string('gcs_address', 100);
            $table->boolean('gcs_status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
