<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class GetFitTableSeeder extends Seeder
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
                    'name' => 'Fitness',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'name' => 'Nutrition',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
            ];
            DB::table('getfit')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }

        
    }
}
