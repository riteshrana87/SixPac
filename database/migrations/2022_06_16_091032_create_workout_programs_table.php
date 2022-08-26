<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workout_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->comment('Program name');
            $table->unsignedBigInteger('plan_day')->comment('Foreign - plan_days id');
            $table->foreign('plan_day')->references('id')->on('plan_days')->onDelete('cascade');
            $table->unsignedBigInteger('goal_id')->comment('Foreign - plan_goals id');
            $table->foreign('goal_id')->references('id')->on('plan_goals')->onDelete('cascade');
            $table->unsignedBigInteger('sport_id')->comment('Foreign - plan_sports id');
            $table->foreign('sport_id')->references('id')->on('plan_sports')->onDelete('cascade');
            $table->string('poster_image')->nullable();
            $table->string('video_name')->nullable();
            $table->string('video_url')->nullable();
            $table->string('overview')->nullable();
            $table->tinyInteger('status')->default(1)->comment("0=>Deactive, 1=>active");
            $table->bigInteger('created_by')->unsigned()->comment('Foreign - Users');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes('deleted_at');
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
        Schema::dropIfExists('workout_programs');
    }
}