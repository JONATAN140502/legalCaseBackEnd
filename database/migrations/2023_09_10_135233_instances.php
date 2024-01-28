<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Instances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->id('ins_id');
            $table->string('ins_nombre', 255);
            $table->unsignedBigInteger('judis_id')->nullable();
            $table->foreign('judis_id')
                ->references('judis_id')
                ->on('judicial_districts')
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
        Schema::dropIfExists('instances');
    }
}
