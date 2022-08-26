<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_galleries', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('post_id')->unsigned()->comment("Foreign Key - Post");
            $table->foreign('post_id')->references('id')->on('user_posts')->onDelete('cascade');
			$table->integer('media_order')->nullable()->comment("Media order no - default set 0");
            $table->string('gallery_name', 255)->nullable();
            $table->text('gallery_description')->nullable();
            $table->string('file_name', 50)->nullable();
            $table->string('file_type', 50)->nullable();
            $table->string('thumb_name', 200)->nullable();
            $table->integer('file_length')->default(0)->comment("In Seconds");
            $table->double('file_size')->default(0)->comment("In KB");
            $table->enum('is_transacoded', array('pending','in-queue','transacoded','failed'))->default('pending')->comment("pending, in-queue, transacoded, failed");
            $table->integer('download_count')->default(0)->comment("In KB");
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 1])->comment("0 = Deactive,1 = Active");
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
        Schema::dropIfExists('post_galleries');
    }
}