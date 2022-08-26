<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HashTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::table('hash_tags')->truncate();
            DB::beginTransaction();
            $data = [
                [
                    'hash_tag_name' => 'smile',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'smiley',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'heart',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'love',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'fashion',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'photooftheday',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'beautiful',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'art',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'photography',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'happy',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'picoftheday',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'cute',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'follow',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'followme',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'nature',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'summer',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'cold',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'winter',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'photo',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'motivation',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'food',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'healthy',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'diet',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'weight_loss',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'weight_gain',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ],
                [
                    'hash_tag_name' => 'sixpac',
                    'status' => 1,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                ]
            ];
            DB::table('hash_tags')->insert($data);
            DB::commit();
        } catch (Exception $e) {
            Log::error('Error to run seeder -> '.$e->getMessage());
        }
    }
}
