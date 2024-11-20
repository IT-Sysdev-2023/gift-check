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
        Schema::create('ledger_budget', function (Blueprint $table) {
            $table->bigInteger('bledger_id', true);
            $table->unsignedInteger('bledger_no');
            $table->integer('bledger_trid');
            $table->dateTime('bledger_datetime');
            $table->string('bledger_type', 30);
            $table->integer('bledger_typeid')->default(0)->index('bledger_typeid');
            $table->integer('bledger_group')->default(0);
            $table->decimal('bdebit_amt', 12)->default(0);
            $table->decimal('bcredit_amt', 12)->default(0);
            $table->integer('btag')->default(0);
            $table->string('bcus_guide', 7)->default('');
            $table->enum('bledger_category', ['regular', 'special'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_budget');
    }
};
