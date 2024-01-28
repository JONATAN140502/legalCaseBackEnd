<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Juzgados extends Migration
{
       /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id('co_id');
            $table->text('co_nombre', 255)->nullable();
            $table->boolean('co_isFavorite')->default(0);
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
        Schema::dropIfExists('courts');
    }
}
