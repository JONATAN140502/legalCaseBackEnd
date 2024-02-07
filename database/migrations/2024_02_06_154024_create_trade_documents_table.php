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

        Schema::create('trade_documents', function (Blueprint $table) {
            $table->id('doc_id');
            $table->string('doc_affair', 255);
            $table->string('doc_description', 255);
            $table->unsignedBigInteger('doc_tra_id');
            $table->foreign('doc_tra_id')->references('tra_id')->on('trades');
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
        Schema::dropIfExists('trade_documents');
    }
};
