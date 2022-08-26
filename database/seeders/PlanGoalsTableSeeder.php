<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class PlanGoalsTableSeeder extends Seeder
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
                        'name' => 'Lose Weight',
                        'created_by' => 1,
                        'icon_file' => "abs.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Lose Fat',
                        'created_by' => 1,
                        'icon_file' => "arms.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Build Muscle',
                        'created_by' => 1,
                        'icon_file' => "back.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Maintain',
                        'created_by' => 1,
                        'icon_file' => "chest.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ]
                ];
            DB::table('plan_goals')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
