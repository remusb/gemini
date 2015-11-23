<?php

namespace App\Repositories;

use DB;

class UserRepository {

  public function getUserFromOAuthClient($client_id) {
    $client = DB::collection('oauth_clients')->where('id', $client_id)->first();

    return DB::collection('users')->find($client['user_id']);
  }

}
