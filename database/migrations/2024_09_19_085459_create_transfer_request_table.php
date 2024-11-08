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
        Schema::create('transfer_request', function (Blueprint $table) {
            $table->integer('tr_reqid', true);
            $table->unsignedInteger('t_reqnum')->index('t_reqnum');
            $table->integer('t_reqstoreby')->index('t_reqstoreby');
            $table->integer('t_reqstoreto')->index('t_reqstoreto');
            $table->dateTime('t_reqdatereq');
            $table->dateTime('t_reqdateneed');
            $table->text('t_reqremarks');
            $table->string('t_reqstatus', 20);
            $table->integer('t_reqby');
            $table->dateTime('t_dateupdated')->nullable();
            $table->integer('t_updatedby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_request');
    }
};
