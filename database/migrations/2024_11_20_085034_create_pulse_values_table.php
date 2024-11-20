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
        Schema::create('pulse_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('timestamp')->index();
            $table->string('type')->index();
            $table->mediumText('key');
            $table->binary('key_hash')->nullable()->virtualAs('unhex(md5(`key`))');
            $table->mediumText('value');

            $table->unique(['type', 'key_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_values');
    }
};
