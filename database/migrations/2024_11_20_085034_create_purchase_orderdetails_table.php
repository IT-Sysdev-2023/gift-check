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
            $table->increments('purchorderdet_ref');
            $table->integer('purchorderdet_mnlno');
            $table->integer('purchorderdet_fadrecno');
            $table->date('purchorderdet_trandate');
            $table->integer('purchorderdet_refno');
            $table->integer('purchorderdet_purono');
            $table->date('purchorderdet_purdate');
            $table->integer('purchorderdet_refpono')->default(0);
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
