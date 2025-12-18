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
        Schema::create('request_audits',function(Blueprint $table){
            $table->id();
            $table->uuid('trace_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('method',10);
            $table->string('path',2048);
            $table->unsignedSmallInteger('status_code');

            $table->unsignedInteger('duration_ms')->nullable();

            $table->string('ip',64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_audits');
    }
};
