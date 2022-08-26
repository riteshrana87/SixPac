<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_followers')->insert([
            0 => [
                'user_id' => '2',
                'follower_id' => '3',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            1 => [
                'user_id' => '2',
                'follower_id' => '4',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            2 => [
                'user_id' => '2',
                'follower_id' => '5',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            3 => [
                'user_id' => '2',
                'follower_id' => '6',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            4 => [
                'user_id' => '3',
                'follower_id' => '4',
                'status' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            5 => [
                'user_id' => '3',
                'follower_id' => '5',
                'status' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            6 => [
                'user_id' => '3',
                'follower_id' => '6',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            7 => [
                'user_id' => '3',
                'follower_id' => '7',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            8 => [
                'user_id' => '3',
                'follower_id' => '8',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            9 => [
                'user_id' => '4',
                'follower_id' => '6',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            10 => [
                'user_id' => '4',
                'follower_id' => '5',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            11 => [
                'user_id' => '4',
                'follower_id' => '2',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            12 => [
                'user_id' => '4',
                'follower_id' => '7',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            13 => [
                'user_id' => '4',
                'follower_id' => '8',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            14 => [
                'user_id' => '5',
                'follower_id' => '8',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            15 => [
                'user_id' => '5',
                'follower_id' => '9',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            16 => [
                'user_id' => '5',
                'follower_id' => '10',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            17 => [
                'user_id' => '6',
                'follower_id' => '2',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            18 => [
                'user_id' => '6',
                'follower_id' => '9',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            19 => [
                'user_id' => '6',
                'follower_id' => '10',
                'status' => '2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
