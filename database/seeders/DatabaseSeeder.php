<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(InterestsTableSeeder::class);
        $this->call(SubInterestsTableSeeder::class);
        $this->call(ProductCategoryTableSeeder::class);
        $this->call(HashTagsTableSeeder::class);
        $this->call(UserFollowersTableSeeder::class);
        $this->call(GetFitTableSeeder::class);
        $this->call(GetfitSearchTypeTableSeeder::class);
        $this->call(BodyPartsTableSeeder::class);
        $this->call(ExerciseDurationsSeeder::class);
        $this->call(PlanDaysTableSeeder::class);
        $this->call(PlanGoalsTableSeeder::class);
        $this->call(FitnessLevelsTableSeeder::class);
        $this->call(AgeGroupsTableSeeder::class);


        
        // \App\Models\User::factory(10)->create();
    }
}