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
        Schema::create('districts', function (Blueprint $table) {
            $table->id('dis_id');
            $table->string('dis_nombre', 255);
            $table->unsignedBigInteger('pro_id');
            $table->unsignedBigInteger('dep_id');
            $table->foreign('pro_id')
                ->references('pro_id')
                ->on('provinces')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('dep_id')
                ->references('dep_id')
                ->on('departments')
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
        Schema::dropIfExists('districts');
    }
};
