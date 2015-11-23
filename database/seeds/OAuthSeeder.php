<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OAuthSeeder extends Seeder {

    public function run()
    {
        $config = app()->make('config');
        $user = DB::collection('users')->first();

        DB::table("oauth_clients")->delete();
        DB::table("oauth_client_endpoints")->delete();

        DB::table("oauth_clients")->insert([
            'id' => $config->get('secrets.client_id'),
            'secret' => $config->get('secrets.client_secret'),
            'name' => 'Gemini',
            'user_id' => $user['_id']
        ]);

        // DB::table("oauth_client_endpoints")->insert([
        //     'client_id' => $config->get('secrets.client_id'),
        //     'redirect_uri' => $config->get('secrets.redirect_uri')
        // ]);
    }

}

?>