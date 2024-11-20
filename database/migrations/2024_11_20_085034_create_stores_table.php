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
        Schema::create('stores', function (Blueprint $table) {
            $table->integer('store_id', true);
            $table->string('store_code', 3);
            $table->string('store_name', 50);
            $table->string('company_code', 3);
            $table->string('default_password', 50);
            $table->string('store_status', 15);
            $table->string('issuereceipt', 5);
            $table->string('store_textfile_ip', 50)->nullable();
            $table->string('store_bng', 5);
            $table->boolean('has_local');
            $table->string('store_initial', 10);
            $table->integer('r_fund')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
