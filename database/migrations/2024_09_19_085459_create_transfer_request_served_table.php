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
        Schema::create('transfer_request_served', function (Blueprint $table) {
            $table->integer('tr_servedid', true);
            $table->unsignedInteger('tr_serverelnum')->index('tr_serverelnum');
            $table->integer('tr_serve_store')->index('tr_serve_store');
            $table->text('tr_serveremarks');
            $table->text('tr_receiveremarks');
            $table->string('tr_serveCheckedBy', 30);
            $table->string('tr_serveReceivedBy', 30);
            $table->dateTime('tr_servedate');
            $table->integer('tr_serveby')->index('tr_serveby');
            $table->string('tr_serveStatus', 10);
            $table->string('tr_serveRecStatus', 20);
            $table->integer('tr_reqid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_request_served');
    }
};
