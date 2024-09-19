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
        Schema::create('promogc_preapproved', function (Blueprint $table) {
            $table->integer('prapp_id', true);
            $table->integer('prapp_reqid');
            $table->string('prapp_doc', 30)->nullable();
            $table->text('prapp_remarks');
            $table->dateTime('prapp_at');
            $table->integer('prapp_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promogc_preapproved');
    }
};
