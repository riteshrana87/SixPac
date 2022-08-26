<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class GetfitSearchTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            
            DB::beginTransaction();
            $data = [
                [
                    'getfit_id' => 1,
                    'name' => 'single exercise',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'getfit_id' => 1,
                    'name' => 'workouts programs',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'getfit_id' => 2,
                    'name' => 'Nutritionists',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'getfit_id' => 2,
                    'name' => 'Recipes',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'getfit_id' => 2,
                    'name' => 'Nutrition programs',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'getfit_id' => 2,
                    'name' => 'Diet foods',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
            ];
            DB::table('getfit_search_type')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
