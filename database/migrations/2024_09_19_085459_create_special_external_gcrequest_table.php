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
        Schema::create('special_external_gcrequest', function (Blueprint $table) {
            $table->integer('spexgc_id', true);
            $table->unsignedInteger('spexgc_num');
            $table->integer('spexgc_reqby');
            $table->dateTime('spexgc_datereq');
            $table->date('spexgc_dateneed');
            $table->text('spexgc_remarks');
            $table->string('spexgc_status', 100);
            $table->string('spexgc_promo', 1);
            $table->integer('spexgc_paid')->default(0);
            $table->string('spexgc_type', 100);
            $table->string('spexgc_reviewed', 30)->default('');
            $table->string('spexgc_released', 30)->default('');
            $table->integer('spexgc_company');
            $table->decimal('spexgc_payment', 12);
            $table->decimal('spexgc_amount', 10)->default(0);
            $table->decimal('spexgc_balance', 10)->default(0);
            $table->string('spexgc_payment_arnum', 30)->nullable();
            $table->string('spexgc_paymentype', 20);
            $table->string('spexgc_receviedby', 100)->default('');
            $table->integer('spexgc_updatedby')->nullable();
            $table->dateTime('spexgc_updated_at')->nullable();
            $table->string('spexgc_payment_stat', 50)->nullable();
            $table->string('spexgc_addemp', 10)->nullable();
            $table->dateTime('spexgc_addempdate')->nullable();
            $table->integer('spexgc_addempaddby')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_external_gcrequest');
    }
};
