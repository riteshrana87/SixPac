<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class BodyPartsTableSeeder extends Seeder
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
                        'name' => 'Abs',
                        'created_by' => 1,
                        'icon_file' => "abs.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Arms',
                        'created_by' => 1,
                        'icon_file' => "arms.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Back',
                        'created_by' => 1,
                        'icon_file' => "back.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Chest',
                        'created_by' => 1,
                        'icon_file' => "chest.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Legs',
                        'created_by' => 1,
                        'icon_file' => "legs.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Shoulders',
                        'created_by' => 1,
                        'icon_file' => "shoulders.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Full Body',
                        'created_by' => 1,
                        'icon_file' => "full_body.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Upper Body',
                        'created_by' => 1,
                        'icon_file' => "upper_body.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => 'Lower Body',
                        'created_by' => 1,
                        'icon_file' => "lower_body.jpg",
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                ];
            DB::table('body_parts')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
