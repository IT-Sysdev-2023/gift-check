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
        Schema::create('suppliergc_item', function (Blueprint $table) {
            $table->integer('suppgci_id', true);
            $table->string('suppgci_itemname', 100);
            $table->integer('suppgci_createdby');
            $table->dateTime('suppgci_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliergc_item');
    }
};
