<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_followers', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - Users");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('follower_id')->unsigned()->comment("Foreign Key - Users");
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('status')->default(1)->comment("0-cancel,1-pending,2-approved,3-block,4-rejected,5-deleted");
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
        Schema::dropIfExists('user_followers');
    }
}
