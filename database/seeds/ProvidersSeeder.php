<?php

use Illuminate\Database\Seeder;

class ProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = DB::collection('users')->first();
        
        DB::collection("providers")->delete();
        DB::collection('providers')->insert([
            'user_id' => $user['_id']
        ]);
    }
}
