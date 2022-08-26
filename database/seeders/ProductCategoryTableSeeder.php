<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::table('product_categories')->truncate();
            DB::beginTransaction();
            $data = [
                [
                    'category_name' => 'Lorem Ipsum',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'category_name' => 'Sample',
                    'created_by' => 1,
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ]
            ];
            DB::table('product_categories')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
