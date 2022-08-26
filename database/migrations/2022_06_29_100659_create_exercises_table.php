<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Exercise title');           
            $table->unsignedBigInteger('workout_type_id')->comment('Foreign - workout_type id');
            $table->foreign('workout_type_id')->references('id')->on('workout_types')->onDelete('cascade');
            $table->unsignedBigInteger('duration_id')->comment('Foreign - exercise_duration id');
            $table->foreign('duration_id')->references('id')->on('exercise_durations')->onDelete('cascade');          
            $table->string('poster_image')->nullable();
            $table->string('video_name')->nullable();
            $table->string('video_thumb')->nullable();
            $table->string('overview')->nullable();
            $table->tinyInteger('gender')->comment('1=Male, 2=>Female, 3=Both');
            $table->tinyInteger('location')->comment('1=Gym, 2=>Home, 3=Anywhere');
            $table->tinyInteger('status')->default(1)->comment("0=>Deactive, 1=>active");
            $table->softDeletes('deleted_at');
            $table->bigInteger('created_by')->unsigned()->comment('Foreign - Users');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercises');
    }
}
