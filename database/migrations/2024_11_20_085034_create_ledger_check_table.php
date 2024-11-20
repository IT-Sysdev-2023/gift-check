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
        Schema::create('ledger_check', function (Blueprint $table) {
            $table->bigInteger('cledger_id', true);
            $table->unsignedInteger('cledger_no');
            $table->dateTime('cledger_datetime');
            $table->string('cledger_type', 5);
            $table->text('cledger_desc');
            $table->integer('cdebit_amt')->default(0);
            $table->integer('ccredit_amt')->default(0);
            $table->integer('c_posted_by');
            $table->integer('c_tag')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_check');
    }
};
