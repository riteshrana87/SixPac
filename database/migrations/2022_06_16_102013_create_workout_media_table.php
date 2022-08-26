<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workout_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->string('thumb_name')->nullable();
            $table->string('short_video')->nullable();
            $table->tinyInteger('is_banner')->default(0)->nullable()->comment('0=Not a banner image, 1=Banner image');
            $table->integer('lengh')->default('0')->comment("In Seconds");
            $table->integer('size')->default('0')->comment("In KB");
            $table->enum('is_transacoded', array('pending','in-queue','transacoded','failed'))->default('pending')->comment("pending, in-queue, transacoded, failed");
            $table->string('workout_mediable_id');
            $table->string('workout_mediable_type');
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
        Schema::dropIfExists('workout_media');
    }
}
