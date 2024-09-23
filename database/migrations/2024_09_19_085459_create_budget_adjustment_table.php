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
        Schema::create('budget_adjustment', function (Blueprint $table) {
            $table->integer('bud_id', true);
            $table->string('bud_adj_type', 10);
            $table->integer('bud_ledger_id');
            $table->text('bud_remark');
            $table->integer('bud_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_adjustment');
    }
};
