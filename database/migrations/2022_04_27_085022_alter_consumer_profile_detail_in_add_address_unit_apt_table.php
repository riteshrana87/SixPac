<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterConsumerProfileDetailInAddAddressUnitAptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consumer_profile_detail', function (Blueprint $table) {
            $table->string('address')->nullable()->after('location_status');
            $table->string('unit_apt')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consumer_profile_detail', function (Blueprint $table) {
            $table->dropColumn('address','unit_apt');
        });
    }
}
