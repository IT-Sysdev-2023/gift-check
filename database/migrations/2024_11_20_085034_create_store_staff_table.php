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
        Schema::create('store_staff', function (Blueprint $table) {
            $table->integer('ss_id', true);
            $table->string('ss_firstname', 50);
            $table->string('ss_lastname', 50);
            $table->string('ss_status', 8);
            $table->string('ss_username', 50)->index('ss_username');
            $table->string('ss_password', 32)->index('ss_password');
            $table->string('ss_idnumber', 12)->index('ss_idnumber');
            $table->string('ss_usertype', 50);
            $table->integer('ss_store')->index('ss_store');
            $table->string('ss_manager_key', 32);
            $table->dateTime('ss_date_created');
            $table->dateTime('ss_date_modified')->nullable()->useCurrent();
            $table->integer('ss_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_staff');
    }
};
