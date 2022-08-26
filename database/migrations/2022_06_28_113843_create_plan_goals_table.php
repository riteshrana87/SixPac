<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_goals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Goal name');
            $table->bigInteger('created_by')->unsigned()->comment('Foreign - Users');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('status')->default(1)->comment("0=>Deactive, 1=>active");
            $table->string('icon_file')->nullable();
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
        Schema::dropIfExists('plan_goals');
    }
}
