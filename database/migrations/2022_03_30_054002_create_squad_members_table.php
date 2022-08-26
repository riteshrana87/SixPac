<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSquadMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('squad_members', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('squad_id')->unsigned()->comment("Foreign Key - squad");
            $table->foreign('squad_id')->references('id')->on('squad')->onDelete('cascade');
            $table->bigInteger('member_id')->unsigned()->comment("Foreign Key - users");
            $table->foreign('member_id')->references('id')->on('users')->onDelete('cascade');
            $table->addColumn('tinyInteger', 'leader_member_flag', ['length' => 1, 'default' => '0'])->comment("0 = Deactive ,1 = Active");
            $table->integer('status')->default(1)->comment("0-cancel,1-pending,2-approved");
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
        Schema::dropIfExists('squad_members');
    }
}
