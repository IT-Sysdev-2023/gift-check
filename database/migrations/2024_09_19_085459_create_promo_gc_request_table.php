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
        Schema::create('promo_gc_request', function (Blueprint $table) {
            $table->integer('pgcreq_id', true);
            $table->unsignedInteger('pgcreq_reqnum')->index('pgcreq_reqnum');
            $table->integer('pgcreq_reqby');
            $table->dateTime('pgcreq_datereq');
            $table->date('pgcreq_dateneeded');
            $table->string('pgcreq_doc', 50);
            $table->string('pgcreq_status', 30);
            $table->string('pgcreq_group_status', 10)->nullable();
            $table->string('pgcreq_remarks', 200);
            $table->decimal('pgcreq_total', 12);
            $table->integer('pgcreq_group');
            $table->integer('pgcreq_tagged');
            $table->string('pgcreq_relstatus', 100)->nullable();
            $table->string('pgcreq_updateby', 11)->nullable();
            $table->dateTime('pgcreq_updatedate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_gc_request');
    }
};
