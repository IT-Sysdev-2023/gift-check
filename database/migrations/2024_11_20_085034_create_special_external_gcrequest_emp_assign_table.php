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
        Schema::create('special_external_gcrequest_emp_assign', function (Blueprint $table) {
            $table->integer('spexgcemp_id', true);
            $table->integer('spexgcemp_trid')->index('spexgcemp_trid');
            $table->integer('payment_id')->default(0);
            $table->decimal('spexgcemp_denom', 12)->index('spexgcemp_denom');
            $table->string('spexgcemp_fname', 30)->default('');
            $table->string('spexgcemp_lname', 40)->default('');
            $table->string('spexgcemp_mname', 30)->default('');
            $table->string('spexgcemp_extname', 10)->default('');
            $table->string('voucher', 300)->default('');
            $table->string('bunit', 100)->default('');
            $table->string('address', 300)->default('');
            $table->string('department', 30)->default('');
            $table->bigInteger('spexgcemp_barcode')->default(0)->index('spexgcemp_barcode');
            $table->string('spexgcemp_review', 3)->default('');
            $table->string('spexgc_status', 10)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_external_gcrequest_emp_assign');
    }
};
