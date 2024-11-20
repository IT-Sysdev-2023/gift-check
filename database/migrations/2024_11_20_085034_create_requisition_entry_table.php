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
        Schema::create('requisition_entry', function (Blueprint $table) {
            $table->integer('requis_id', true);
            $table->unsignedInteger('requis_erno');
            $table->string('requis_rmno', 11)->default('');
            $table->date('requis_req');
            $table->date('requis_need')->nullable();
            $table->string('requis_loc', 20);
            $table->string('requis_dept', 20);
            $table->text('requis_rem');
            $table->integer('requis_supplierid');
            $table->integer('repuis_pro_id');
            $table->integer('requis_req_by');
            $table->string('requis_approved', 50)->default('');
            $table->string('requis_checked', 50);
            $table->integer('requis_status')->default(0);
            $table->string('requis_foldersaved', 15);
            $table->unsignedInteger('requis_ledgeref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_entry');
    }
};
