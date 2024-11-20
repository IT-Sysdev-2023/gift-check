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
        Schema::create('store_local_server', function (Blueprint $table) {
            $table->integer('stlocser_id', true);
            $table->integer('stlocser_storeid');
            $table->string('stlocser_ip', 20);
            $table->string('stlocser_username', 20);
            $table->string('stlocser_password', 30);
            $table->string('stlocser_db', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_local_server');
    }
};
