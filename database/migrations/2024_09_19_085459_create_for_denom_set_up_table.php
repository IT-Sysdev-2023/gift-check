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
        Schema::create('for_denom_set_up', function (Blueprint $table) {
            $table->integer('fds_id', true);
            $table->integer('fds_denom_reqid');
            $table->integer('fds_denom');
            $table->string('fds_status', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('for_denom_set_up');
    }
};
