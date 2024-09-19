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
        Schema::create('ledger_store', function (Blueprint $table) {
            $table->integer('sledger_id', true);
            $table->unsignedBigInteger('sledger_no');
            $table->dateTime('sledger_date');
            $table->integer('sledger_ref');
            $table->string('sledger_trans', 11);
            $table->decimal('sledger_trans_disc', 15)->default(0);
            $table->string('sledger_desc', 50);
            $table->decimal('sledger_debit', 15)->default(0);
            $table->decimal('sledger_credit', 15)->default(0);
            $table->integer('sledger_store');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_store');
    }
};
