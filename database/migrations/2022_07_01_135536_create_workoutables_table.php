<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workoutables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workout_id')->comment('Foreign - workouts id');
            $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('cascade');
            $table->string('workoutable_type');
            $table->integer('workoutable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workoutables');
    }
}