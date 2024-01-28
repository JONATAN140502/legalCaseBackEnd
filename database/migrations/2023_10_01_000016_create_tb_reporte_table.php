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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('rep_id');
            $table->timestamp('rep_fecha_generacion');
            $table->string('rep_tipo', 255)->comment('excel, pdf, etc');
            $table->unsignedBigInteger('usu_id')->nullable()->comment('usuario reporte');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usu_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
