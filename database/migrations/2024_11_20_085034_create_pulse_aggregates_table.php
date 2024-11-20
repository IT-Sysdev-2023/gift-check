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
        Schema::create('pulse_aggregates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('bucket');
            $table->unsignedMediumInteger('period');
            $table->string('type')->index();
            $table->mediumText('key');
            $table->binary('key_hash')->nullable()->virtualAs('unhex(md5(`key`))');
            $table->string('aggregate');
            $table->decimal('value', 20);
            $table->unsignedInteger('count')->nullable();

            $table->unique(['bucket', 'period', 'type', 'aggregate', 'key_hash']);
            $table->index(['period', 'bucket']);
            $table->index(['period', 'type', 'aggregate', 'bucket']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_aggregates');
    }
};
