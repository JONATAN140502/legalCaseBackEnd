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
        Schema::create('successors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fallecido_id')->nullable();
            $table->foreign('fallecido_id')
                ->references('per_id')
                ->on('persons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('sucesor_id')->nullable();
                $table->foreign('sucesor_id')
                    ->references('per_id')
                    ->on('persons')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('persons', function (Blueprint $table) {
            $table->integer('fallecido')->default(0)->comment('0->vivo,1->fallecido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('successors');
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('fallecido');
        });

    }
};
