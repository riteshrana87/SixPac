<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_interests', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('interest_id')->unsigned()->comment("Foreign Key - interests");
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('cascade');
            $table->string('sub_interest_name')->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 1])->comment("0 = Deactive ,1 = Active");
            $table->addColumn('tinyInteger', 'other', ['length' => 1, 'default' => 0])->comment("0 = Deactive ,1 = Active");
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('sub_interests');
    }
}
