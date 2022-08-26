<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('role')->nullable()->comment('1 = Super Admin, 2 = Admin, 3 = Business User, 4 = Employee, 5 = Consumer');
            $table->string('name')->nullable();
            $table->string('user_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->bigInteger('phone')->unique()->nullable();
            $table->string('facebook_id', 100)->nullable();
            $table->string('google_id', 100)->nullable();
            $table->string('apple_id', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('password')->nullable();
            $table->string('avtar')->nullable();
            $table->string('banner_pic')->nullable();
            $table->integer('app_version')->nullable();
            $table->addColumn('tinyInteger', 'status', ['length' => 1, 'default' => '0'])->comment("0 = Deactive ,1 = Active");
            $table->integer('social_flag')->nullable()->comment('0 = Sixpec , 1 = Google, 2 = Facebook, 3 = Apple');
            $table->integer('gender')->default(1)->comment('1 = male , 2 = female,3 = other');
            $table->string('gender_pronoun')->nullable();
            $table->string('referral_code')->nullable();
            $table->addColumn('tinyInteger', 'is_email_verified', ['length' => 1, 'default' => 0])->comment("0 = Deactive ,1 = Active");
            $table->addColumn('tinyInteger', 'is_mobile_verified', ['length' => 1, 'default' => 0])->comment("0 = Deactive ,1 = Active");
            $table->addColumn('tinyInteger', 'otp_verified', ['length' => 1, 'default' => 0])->comment("0 = Deactive ,1 = Active");
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}