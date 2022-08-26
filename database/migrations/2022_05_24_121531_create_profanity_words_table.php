<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfanityWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profanity_words', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('word',50)->nullable();
			$table->integer('match_method')->default(0);
			$table->tinyInteger('status')->default(1)->comment("0 = Deactive ,1 = Active");
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
        Schema::dropIfExists('profanity_words');
    }
}