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
        Schema::create('pulse_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('timestamp')->index();
            $table->string('type')->index();
            $table->mediumText('key');
            $table->binary('key_hash')->nullable()->virtualAs('unhex(md5(`key`))')->index();
            $table->bigInteger('value')->nullable();

            $table->index(['timestamp', 'type', 'key_hash', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_entries');
    }
};
