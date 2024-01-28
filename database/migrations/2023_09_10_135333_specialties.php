<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Specialties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id('esp_id');
            $table->string('esp_nombre', 255);
            $table->unsignedBigInteger('ins_id')->nullable();
            $table->foreign('ins_id')
                ->references('ins_id')
                ->on('instances')
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
        Schema::dropIfExists('specialties');
    }
}
