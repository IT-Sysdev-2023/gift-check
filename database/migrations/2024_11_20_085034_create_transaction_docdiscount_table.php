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
        Schema::create('transaction_docdiscount', function (Blueprint $table) {
            $table->integer('trdocdisc_id', true);
            $table->integer('trdocdisc_trid')->index('trdocdisc_trid');
            $table->integer('trdocdisc_disctype');
            $table->decimal('trdocdisc_prcnt', 12, 5);
            $table->decimal('trdocdisc_amnt', 12);
            $table->integer('trdocdisc_superby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_docdiscount');
    }
};
