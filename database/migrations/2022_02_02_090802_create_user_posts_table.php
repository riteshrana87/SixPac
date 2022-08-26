<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_posts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - users");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('post_title', 255)->nullable();
            $table->string('post_slug', 255)->nullable();
            $table->text('post_content')->nullable();
            $table->text('notes')->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 1])->comment("0 = Deactive,1-active");
            $table->addColumn('tinyInteger', 'share_status', ['length' => 1, 'default' => 0])->comment("0 = Deactive,1-active");
            $table->addColumn('tinyInteger', 'is_public', ['length' => 1, 'default' => 0])->comment("0 = false,1 = true");
            $table->timestamp('deleted_at')->nullable()->comment("Soft Deletes");
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('user_posts');
    }
}