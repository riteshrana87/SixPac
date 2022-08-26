<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExerciseDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_exercise_data', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - users");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('exercise_id')->nullable();
            $table->string('calory')->nullable();
            $table->string('time_spend')->nullable();
            $table->text('notes')->nullable();
            $table->text('description')->nullable();
            $table->string('met')->nullable();
            $table->timestamp('date_and_time')->nullable();
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
        Schema::dropIfExists('user_exercise_data');
    }
}
