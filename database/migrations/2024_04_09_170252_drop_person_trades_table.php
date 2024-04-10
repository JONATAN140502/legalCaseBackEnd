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
        Schema::dropIfExists('person_trades');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::create('person_trades', function ($table) {
            $table->id();
            $table->unsignedBigInteger('pt_per_id');
            $table->foreign('pt_per_id')->references('per_id')->on('persons');
            $table->unsignedBigInteger('pt_tra_id');
            $table->foreign('pt_tra_id')->references('tra_id')->on('trades');
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::enableForeignKeyConstraints();
    }
};
