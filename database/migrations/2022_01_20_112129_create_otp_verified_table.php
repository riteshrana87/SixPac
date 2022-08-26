<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpVerifiedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_verified', function (Blueprint $table) {
            $table->id();
            $table->integer('otp')->nullable();
            // $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - User");
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->string('email')->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->integer('is_email')->default('1')->comment('0 = mobile , 1 = email');
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => '1'])->comment("0 = Deactive ,1 = Active");
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
        Schema::dropIfExists('otp_verified');
    }
}