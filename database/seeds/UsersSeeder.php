<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::collection("users")->delete();
        
        DB::collection('users')->insert([
            'first_name' => str_random(12),
            'last_name' => str_random(12)
        ]);
    }
}
