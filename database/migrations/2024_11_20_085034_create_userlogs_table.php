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
        Schema::create('userlogs', function (Blueprint $table) {
            $table->comment('1 = users 2 = store_staff');
            $table->integer('logs_id', true);
            $table->integer('logs_userid');
            $table->dateTime('logs_datetime');
            $table->integer('logs_usertype');
            $table->string('logs_ip_address', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userlogs');
    }
};
