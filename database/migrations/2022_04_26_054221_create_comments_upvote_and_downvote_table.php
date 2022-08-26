<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsUpvoteAndDownvoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_upvote_and_downvote', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('comments_id')->unsigned()->nullable()->comment('Foreign - Post Comment');
            $table->foreign('comments_id')->references('id')->on('post_comments')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->nullable()->comment('Foreign - User');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => '1'])->comment("0 = Downvote ,1 = Upvote");
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
        Schema::dropIfExists('comments_upvote_and_downvote');
    }
}
