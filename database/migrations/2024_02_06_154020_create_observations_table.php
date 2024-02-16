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

        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->string('obs_title', 50);
            $table->string('obs_description', 255);
            $table->string('obs_derivative', 50)->nullable();
            $table->char('obs_state', 1);
            $table->bigInteger('obs_fil_id');
            $table->foreign('obs_fil_id')->references('tra_id')->on('trades');
            $table->bigInteger('obs_law_id');
            $table->foreign('obs_law_id')->references('law_id')->on('lawyers');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
