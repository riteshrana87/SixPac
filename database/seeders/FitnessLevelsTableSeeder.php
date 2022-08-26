<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class FitnessLevelsTableSeeder extends Seeder
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
                        'name' => 'Beginner',
                        'created_by' => 1,
                        'icon_file' => "abs.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Intermediate',
                        'created_by' => 1,
                        'icon_file' => "arms.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Advanced',
                        'created_by' => 1,
                        'icon_file' => "back.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ]
                ];
            DB::table('fitness_levels')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
