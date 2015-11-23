<?php

namespace App\Repositories;

use DB;

class FacebookServiceRepository implements ServiceRepository {
  const SERVICE_CODE = 'fb';

  public function saveToken($user_id, $data) {
    DB::collection('providers')->where('user_id', $user_id)->where('service', FacebookServiceRepository::SERVICE_CODE)->update(
      ['user_id' => $user_id, 'service' => FacebookServiceRepository::SERVICE_CODE, 'facebook_access_token' => $data['token'], 
        'expires_at' => $data['expires_at']], array('upsert' => true));
  }

  public function getUserProvider($user_id) {
    return DB::collection('providers')->where('user_id', $user_id)->first();
  }

}