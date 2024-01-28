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
        Schema::create('procesals', function (Blueprint $table) {
            $table->id('proc_id');
            //natural o jurica
            $table->string('tipo_procesal', 55)->nullable();
            $table->string('tipo_persona', 55)->nullable();
            $table->unsignedBigInteger('per_id')->nullable();
            $table->unsignedBigInteger('exp_id')->nullable();
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('proceedings')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('per_id')
                ->references('per_id') 
                ->on('persons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesals');
    }
};
