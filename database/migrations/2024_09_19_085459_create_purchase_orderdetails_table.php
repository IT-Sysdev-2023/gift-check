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
        Schema::create('purchase_orderdetails', function (Blueprint $table) {
            $table->integer('purchorderdet_ref', true);
            $table->integer('purchorderdet_mnlno');
            $table->integer('purchorderdet_fadrecno');
            $table->date('purchorderdet_trandate');
            $table->string('purchorderdet_refno', 50)->default('');
            $table->string('purchorderdet_purono', 50)->default('');
            $table->date('purchorderdet_purdate');
            $table->string('purchorderdet_refpono', 50)->default('0');
            $table->integer('purchorderdet_payterms');
            $table->string('purchorderdet_locode', 40);
            $table->string('purchorderdet_deptcode', 50);
            $table->string('purchorderdet_supname', 50);
            $table->string('purchorderdet_modpay', 20);
            $table->text('purchorderdet_remarks');
            $table->string('purchorderdet_prepby', 60);
            $table->string('purchorderdet_checkby', 60);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orderdetails');
    }
};
