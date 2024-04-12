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
        Schema::dropIfExists('observations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->id('obs_id');
            $table->string('obs_title', 50);
            $table->string('obs_description', 255);
            $table->string('obs_derivative', 50)->nullable();
            $table->char('obs_state', 1);
            $table->unsignedBigInteger('obs_tra_id');
            $table->foreign('obs_tra_id')->references('tra_id')->on('trades');
            $table->unsignedBigInteger('obs_abo_id');
            $table->foreign('obs_abo_id')->references('abo_id')->on('lawyers');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
