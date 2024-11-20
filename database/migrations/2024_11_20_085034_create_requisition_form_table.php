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
        Schema::create('requisition_form', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->integer('req_no')->default(0);
            $table->string('sup_name');
            $table->string('mop');
            $table->string('rec_no')->default('');
            $table->dateTime('trans_date');
            $table->string('ref_no')->default('');
            $table->string('po_no')->default('');
            $table->string('pay_terms')->default('');
            $table->string('loc_code')->default('');
            $table->dateTime('pur_date');
            $table->string('ref_po_no', 50)->default('');
            $table->string('dep_code', 50)->default('');
            $table->string('remarks', 500)->default('');
            $table->string('prep_by', 500)->default('');
            $table->string('check_by', 500)->default('');
            $table->string('used', 500)->nullable();
            $table->string('srr_type', 500)->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_form');
    }
};
