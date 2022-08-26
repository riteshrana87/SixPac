<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('sender_id')->unsigned()->comment("Foreign Key - User");
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('no action');
            $table->bigInteger('receiver_id')->unsigned()->nullable()->comment("Foreign Key - User");
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('no action');
            $table->string('notification_type',255)->comment("Notification type");
            $table->bigInteger('post_id')->comment("post id")->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 0])->comment("0 = unread,1 = read");
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
        Schema::dropIfExists('notification');
    }
}
