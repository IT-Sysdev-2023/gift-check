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
        Schema::create('transfer_request_items', function (Blueprint $table) {
            $table->integer('tr_itemsid', true);
            $table->integer('tr_itemsdenom')->index('tr_itemsdenom');
            $table->integer('tr_itemsqty');
            $table->integer('tr_itemsqtyremain');
            $table->integer('tr_itemsreqid')->index('tr_itemsreqid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_request_items');
    }
};
