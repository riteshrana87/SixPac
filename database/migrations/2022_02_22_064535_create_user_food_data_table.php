<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFoodDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_food_data', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - User");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('fdiid')->nullable();
            $table->decimal('protein', 18, 2)->nullable();
            $table->decimal('carbohydrate', 18, 2)->nullable();
            $table->decimal('calory', 18, 2)->nullable();
            $table->decimal('energy', 18, 2)->nullable();
            $table->string('quantity')->nullable();
            $table->enum('type_of_meal', array('Breakfast','Lunch','Snack','Dinner','Other'));
            $table->text('food_description')->nullable();
            $table->timestamp('date_and_time')->nullable();
            $table->addColumn('tinyInteger', 'food_or_exercise', ['length' => 1, 'default' => 0])->comment("0 = Food,1 = Exercise");
            $table->string('serving_qty')->nullable();
            $table->string('serving_size')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('user_food_data');
    }
}
