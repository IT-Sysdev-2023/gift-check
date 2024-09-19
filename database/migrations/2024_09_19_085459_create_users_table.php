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
            $table->string('emp_id', 15);
            $table->string('username', 20)->index('username');
            $table->string('password')->index('password');
            $table->string('firstname', 40);
            $table->string('lastname', 50);
            $table->string('usertype', 40)->index('usertype');
            $table->string('usergroup', 4);
            $table->string('user_status', 25)->index('user_status');
            $table->integer('user_role');
            $table->string('ip_address', 20)->default('');
            $table->string('login', 3);
            $table->integer('promo_tag');
            $table->integer('store_assigned');
            $table->dateTime('date_created');
            $table->dateTime('date_updated')->nullable();
            $table->integer('user_addby');
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
