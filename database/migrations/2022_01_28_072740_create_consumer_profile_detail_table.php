<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumerProfileDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumer_profile_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - User");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('preferred_pronoun')->nullable();
            $table->addColumn('tinyInteger', 'location_status', ['length' => 1, 'default' => '1'])->comment("0-false,1-true");
            $table->integer('city')->nullable();
            $table->integer('state')->nullable();
            $table->integer('country')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->integer('activity_level')->nullable();
            $table->string('daily_calories')->nullable();
            $table->string('burned_calory')->nullable();
            $table->string('target_weight')->nullable();
            $table->string('starting_weight')->nullable();
            $table->string('weight_gain_loss_frequency')->nullable();
            $table->string('weight_goal')->nullable();
			$table->string('activity_frequency')->nullable();
			$table->string('zipcode')->nullable();
            $table->text('fitness_status')->nullable();
            $table->date('goal_completion_date')->nullable();
            $table->decimal('measurement_type', 18, 2)->nullable();
            $table->addColumn('tinyInteger', 'update_data', ['length' => 1, 'default' => '0'])->comment("0 = Deactive ,1 = Active");
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
        Schema::dropIfExists('consumer_profile_detail');
    }
}
