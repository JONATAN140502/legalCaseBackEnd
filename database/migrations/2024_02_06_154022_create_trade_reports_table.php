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
        Schema::disableForeignKeyConstraints();

        Schema::create('trade_reports', function (Blueprint $table) {
            $table->id('rep_id');
            $table->string('rep_asunto', 255);
            $table->unsignedBigInteger('rep_tra_id');
            $table->foreign('rep_tra_id')->references('tra_id')->on('trades');
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_reports');
    }
};
