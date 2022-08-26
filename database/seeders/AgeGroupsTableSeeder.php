<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class AgeGroupsTableSeeder extends Seeder
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
                        'name' => '18 - 29',
                        'created_by' => 1,
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => '30 - 39',
                        'created_by' => 1,
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => '40 - 59',
                        'created_by' => 1,
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ],
                    [
                        'name' => '60+',
                        'created_by' => 1,
                        'status' => 1,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now(),
                    ]
                ];
            DB::table('age_groups')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
