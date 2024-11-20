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
        Schema::create('approved_request', function (Blueprint $table) {
            $table->integer('reqap_id', true);
            $table->integer('reqap_trid')->index('reqap_trid');
            $table->string('reqap_approvedtype', 120)->index('reqap_approvedtype');
            $table->text('reqap_remarks');
            $table->string('reqap_doc', 200)->default('');
            $table->string('reqap_checkedby', 100)->nullable();
            $table->string('reqap_approvedby', 100)->nullable();
            $table->integer('reqap_preparedby');
            $table->dateTime('reqap_date');
            $table->integer('reqap_trnum')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_request');
    }
};
