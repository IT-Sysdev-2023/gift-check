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
        Schema::create('temp_sales_docdiscount', function (Blueprint $table) {
            $table->integer('docdis_id', true);
            $table->integer('docdis_cashierid');
            $table->integer('docdis_superid');
            $table->integer('docdis_discountype');
            $table->integer('docdis_transacid');
            $table->decimal('docdis_pecentage', 12, 4);
            $table->decimal('docdis_amt', 12);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_sales_docdiscount');
    }
};
