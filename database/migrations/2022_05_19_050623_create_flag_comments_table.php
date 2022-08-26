<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlagCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flag_comments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('post_id')->unsigned()->comment('Foreign - Post');
            $table->foreign('post_id')->references('id')->on('user_posts')->onDelete('cascade');
            $table->bigInteger('comment_id')->unsigned()->nullable()->comment('Foreign - Post Comment');
            $table->foreign('comment_id')->references('id')->on('post_comments')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->comment('Foreign - User');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('flag_comments');
    }
}