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
        Schema::table('trade_reports', function (Blueprint $table) {
            //Año
            $table->integer('rep_anio');
            $table->string('rep_informe')->nullable()->change();
            $table->string('rep_oficio')->nullable()->change();
            $table->string('rep_pdf_oficio', 255)->nullable();
            $table->string('rep_pdf_informe', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trade_reports', function (Blueprint $table) {
            $table->dropColumn('rep_anio');
            $table->string('rep_informe')->nullable(false)->change();
            $table->string('rep_oficio')->nullable(false)->change();
            $table->dropColumn('rep_pdf_oficio');
            $table->dropColumn('rep_pdf_informe');
        });
    }
};
