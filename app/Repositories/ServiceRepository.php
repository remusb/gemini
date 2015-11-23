<?php

namespace App\Repositories;

interface ServiceRepository {
  public function saveToken($user_id, $data);

  public function getUserProvider($user_id);
}
