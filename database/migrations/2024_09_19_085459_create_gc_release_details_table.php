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
        Schema::create('gc_release_details', function (Blueprint $table) {
            $table->integer('gcrd_id', true);
            $table->dateTime('gcrd_datetime');
            $table->integer('gcrd_reqid')->index('gcrd_reqid');
            $table->integer('gcrd_appby');
            $table->integer('gcrd_checkby');
            $table->integer('gcrd_relby');
            $table->integer('gcrd_recby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_release_details');
    }
};
