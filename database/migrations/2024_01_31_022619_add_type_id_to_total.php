<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTypeIdToTotal extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')
                ->references('id')
                ->on('proceeding_types')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')
                ->references('id')
                ->on('proceeding_types')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        Schema::table('instances', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')
                ->references('id')
                ->on('proceeding_types')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        Schema::table('specialties', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')
                ->references('id')
                ->on('proceeding_types')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        Schema::table('courts', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable();
            $table->foreign('type_id')
                ->references('id')
                ->on('proceeding_types')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        DB::table('claims')->update(['type_id' => 1]);
        DB::table('subjects')->update(['type_id' => 1]);
        DB::table('instances')->update(['type_id' => 1]);
        DB::table('specialties')->update(['type_id' => 1]);
        DB::table('courts')->update(['type_id' => 1]);

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
        Schema::table('specialties', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
        Schema::table('courts', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
        Schema::table('instances', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
    }
}
