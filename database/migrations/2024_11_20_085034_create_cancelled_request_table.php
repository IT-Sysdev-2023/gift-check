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
        Schema::create('cancelled_request', function (Blueprint $table) {
            $table->integer('reqcan_id', true);
            $table->integer('reqcan_trid');
            $table->string('reqcan_canceltype', 50);
            $table->text('reqcan_remarks');
            $table->string('reqcan_doc', 50);
            $table->integer('reqcan_checkedby')->nullable();
            $table->integer('reqcan_approvedby')->nullable();
            $table->integer('reqcan_preparedby')->nullable();
            $table->date('reqcan_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_request');
    }
};
