<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workout_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('getfit_id')->comment('Foreign - getfit id');
            $table->bigInteger('categories_id')->comment('Foreign - workout_categories id');
            $table->bigInteger('designated_id')->comment('Foreign - on_demand_services id');
            $table->bigInteger('body_parts_id')->comment('Foreign - body_parts id');
            $table->bigInteger('workouts_type_id')->comment('Foreign - getfit_search_type id');
            $table->string('name')->nullable();      
            $table->text('description')->nullable();         
            $table->string('duration')->nullable();
            $table->string('location')->nullable();              
            $table->string('fitness_level')->nullable();              
            $table->json('age_group')->nullable();
            $table->integer('gender')->default(1)->comment('1 = male , 2 = female,3 = other');
            $table->integer('program_duration')->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 1])->comment("0 = Deactive ,1 = Active");
            $table->addColumn('tinyInteger', 'is_public', ['length' => 1, 'default' => 1])->comment("0 = false,1 = true");
            $table->addColumn('tinyInteger', 'Workout_flag', ['length' => 1, 'default' => 1])->comment("1 = Exercise,2 = Workout,3 = Program");
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
        Schema::dropIfExists('workout_details');
    }
}
