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
        Schema::create('transaction_linediscount', function (Blueprint $table) {
            $table->integer('trlinedis_id', true);
            $table->integer('trlinedis_sid')->index('trlinedis_sid');
            $table->bigInteger('trlinedis_barcode')->index('trlinedis_barcode');
            $table->integer('trlinedis_disctype');
            $table->decimal('trlinedis_discpercent', 12, 5);
            $table->decimal('trlinedis_discamt', 12);
            $table->integer('trlinedis_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_linediscount');
    }
};
