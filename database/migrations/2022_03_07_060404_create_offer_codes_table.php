<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_codes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->string('offer_code')->nullable();
            $table->float('discount')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('created_by')->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 1])->comment("0 = Deactive ,1 = Active");
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
        Schema::dropIfExists('offer_codes');
    }
}
