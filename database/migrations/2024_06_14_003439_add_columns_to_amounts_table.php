<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('execution_amounts', function (Blueprint $table) {
            $table->decimal('total_amount_sentence', 10, 2)->nullable()->after('ex_costos');
            $table->decimal('total_balance_payable', 10, 2)->nullable()->after('total_amount_sentence');
        });
    }

    public function down()
    {
        Schema::table('execution_amounts', function (Blueprint $table) {
            $table->dropColumn('total_amount_sentence');
            $table->dropColumn('total_balance_payable');
        });
    }
};
