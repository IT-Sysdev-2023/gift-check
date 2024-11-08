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
        Schema::create('special_external_gcrequest_items', function (Blueprint $table) {
            $table->integer('specit_id', true);
            $table->decimal('specit_denoms', 10)->index('specit_denoms');
            $table->integer('specit_qty');
            $table->integer('specit_trid')->index('specit_trid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_external_gcrequest_items');
    }
};
