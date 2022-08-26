<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            //DB::table('interests')->truncate();
            DB::beginTransaction();
            $data = [
                [
                    'interest_name' => 'Strength & Conditioning',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'interest_name' => 'Cardio & Endurance',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'interest_name' => 'Yoga & Meditation',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'interest_name' => 'Bootcamp & Classes',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'interest_name' => 'Outdoor & Sports',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'interest_name' => 'Gym Affiliations & Integrations',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ]
            ];
            DB::table('interests')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
