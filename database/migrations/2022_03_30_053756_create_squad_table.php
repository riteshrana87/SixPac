<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSquadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('squad', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->string('squad_name')->nullable();
            $table->string('squad_profile_pic')->nullable();
            $table->string('banner_pic')->nullable();
            $table->longText('notes')->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => '1'])->comment("0 = Deactive ,1 = Active");
            $table->addColumn('tinyInteger', 'is_public', ['length' => 1, 'default' => 0])->comment("0 = false,1 = true");
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
        Schema::dropIfExists('squad');
    }
}
