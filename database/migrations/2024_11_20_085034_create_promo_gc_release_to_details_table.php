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
        Schema::create('promo_gc_release_to_details', function (Blueprint $table) {
            $table->integer('prrelto_id', true);
            $table->integer('prrelto_relnumber');
            $table->integer('prrelto_trid')->index('prrelto_trid');
            $table->string('prrelto_docs', 100);
            $table->string('prrelto_checkedby', 100);
            $table->string('prrelto_approvedby', 100);
            $table->integer('prrelto_relby');
            $table->dateTime('prrelto_date');
            $table->text('prrelto_remarks');
            $table->string('prrelto_recby', 100);
            $table->string('prrelto_status', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_gc_release_to_details');
    }
};
