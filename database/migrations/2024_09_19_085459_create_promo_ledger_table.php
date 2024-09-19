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
        Schema::create('promo_ledger', function (Blueprint $table) {
            $table->integer('promled_id', true);
            $table->string('promled_desc', 40);
            $table->decimal('promled_debit', 12)->nullable();
            $table->decimal('promled_credit', 12)->nullable();
            $table->integer('promled_trid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_ledger');
    }
};
