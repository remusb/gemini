<?php

namespace App\Repositories;

use DB;

class TwitterServiceRepository implements ServiceRepository {
  const SERVICE_CODE = 'tw';

  public function saveToken($user_id, $data) {
    $data = array_merge($data, ['user_id' => $user_id, 'service' => TwitterServiceRepository::SERVICE_CODE]);

    DB::collection('providers')->where('user_id', $user_id)->where('service', TwitterServiceRepository::SERVICE_CODE)->update(
      $data, array('upsert' => true));
  }

  public function getUserProvider($user_id) {
    return DB::collection('providers')->where('user_id', (string) $user_id)->first();
  }

}