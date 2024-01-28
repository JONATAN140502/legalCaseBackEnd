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
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id('doc_id');
            $table->string('doc_nombre', 255)->nullable();
            $table->string('doc_tipo', 255)->nullable();
            $table->text('doc_desciprcion')->nullable();
            $table->string('doc_ruta_archivo', 255)->nullable();
            $table->unsignedBigInteger('exp_id');
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('proceedings');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
