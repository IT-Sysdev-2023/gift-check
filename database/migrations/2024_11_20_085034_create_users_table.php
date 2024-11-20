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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('user_id', true);
            $table->string('emp_id', 15)->nullable();
            $table->string('username', 20)->nullable()->index('username');
            $table->string('usergroup', 20)->nullable();
            $table->string('password')->nullable()->index('password');
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 50)->nullable();
            $table->string('usertype', 40)->nullable()->index('usertype');
            $table->string('user_status', 25)->nullable()->index('user_status');
            $table->integer('user_role')->nullable();
            $table->string('ip_address', 20)->nullable()->default('');
            $table->string('login', 3)->nullable();
            $table->integer('promo_tag')->nullable();
            $table->string('it_type', 50)->nullable();
            $table->string('retail_group', 50)->nullable();
            $table->string('store_assigned', 50)->nullable()->default('0');
            $table->dateTime('date_created')->nullable();
            $table->dateTime('date_updated')->nullable();
            $table->integer('user_addby')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
