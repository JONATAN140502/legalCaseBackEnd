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
        Schema::dropIfExists('assistants');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('assistants', function (Blueprint $table) {
            $table->id('ass_id');
            $table->string('ass_carga_laboral', 255)->nullable()->comment('cantidad de casos');
            $table->string('ass_disponibilidad', 255)->nullable()->comment('ocupado o libre');
            $table->unsignedBigInteger('per_id')->nullable();
            $table->foreign('per_id')
                ->references('per_id')
                ->on('persons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
