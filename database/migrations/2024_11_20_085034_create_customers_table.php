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
        Schema::create('customers', function (Blueprint $table) {
            $table->integer('cus_id', true);
            $table->string('cus_fname', 50);
            $table->string('cus_lname', 50);
            $table->string('cus_mname', 50);
            $table->string('cus_namext', 10);
            $table->date('cus_dob')->nullable();
            $table->integer('cus_sex')->nullable();
            $table->integer('cus_cstatus')->default(0);
            $table->string('cus_idnumber', 50)->nullable();
            $table->text('cus_address')->nullable();
            $table->string('cus_mobile', 15)->nullable();
            $table->integer('cus_store_register');
            $table->date('cus_register_at');
            $table->integer('cus_register_by');
            $table->string('cus_store_updated', 5)->default('');
            $table->dateTime('cus_updated_at')->nullable();
            $table->string('cus_updated_by', 5)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
