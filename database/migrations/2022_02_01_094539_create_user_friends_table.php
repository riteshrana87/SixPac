<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_friends', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('sender_id')->unsigned()->comment("Foreign Key - Users");
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('receiver_id')->unsigned()->comment("Foreign Key - Users");
           $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 0])->comment("0-Pending, 1-Approved,2-cancelled");
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
        Schema::dropIfExists('user_friends');
    }
}
