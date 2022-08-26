<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExercisablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercisables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id')->comment('Foreign - exercises id');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->string('exercisable_type');
            $table->integer('exercisable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercisables');
    }
}
