<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('post_id')->unsigned()->comment('Foreign - Post');
            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Foreign - Post Comment');
            $table->bigInteger('user_id')->unsigned()->comment('Foreign - User');
            //$table->morphs('commentable');
            $table->nullableMorphs('commentable');
            $table->text('comment')->nullable();
            $table->string('comments_image')->nullable();
            $table->string('comments_video')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('user_posts')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('post_comments');
            $table->softDeletes();
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
        Schema::dropIfExists('post_comments');
    }
}