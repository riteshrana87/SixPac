<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_token', function (Blueprint $table) {
            $table->id();
            $table->string('device_token')->nullable();
            $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - User");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('device_type', array('web','ios','android'));
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
        Schema::dropIfExists('device_token');
    }
}
