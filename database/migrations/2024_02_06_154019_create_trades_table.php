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

        Schema::create('trades', function (Blueprint $table) {
            $table->id('tra_id');
            $table->string('tra_number', 50);
            $table->string('tra_number_ext', 50);
            $table->string('tra_matter', 255);
            $table->date('tra_arrival_date');
            $table->char('tra_format', 1);
            $table->char('tra_state_mp', 1);
            $table->char('tra_state_law', 1);
            $table->string('tra_ubication', 50);
            $table->unsignedBigInteger('tra_are_id');
            $table->foreign('tra_are_id')->references('are_id')->on('areas');
            $table->unsignedBigInteger('tra_abo_id')->nullable();
            $table->foreign('tra_abo_id')->references('abo_id')->on('lawyers');
            $table->unsignedBigInteger('tra_per_id');
            $table->foreign('tra_per_id')->references('per_id')->on('persons');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
