<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("Primary Key");
            $table->bigInteger('category_id')->unsigned()->comment("Foreign Key - product category");
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned()->comment("Foreign Key - users");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('product_title', 255)->nullable();
            $table->string('product_slug', 255)->nullable();
            $table->text('product_description')->nullable();
			$table->text('sku')->nullable();
			$table->integer('quantity')->nullable();
			$table->double('cost_price')->nullable();
			$table->double('sell_price')->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => 1])->comment("0 = Deactive,1-active");
            $table->timestamp('deleted_at')->nullable()->comment("Soft Deletes");
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
        Schema::dropIfExists('products');
    }
}
