<?php

namespace App\Services;

interface AbstractService {

  public static function scopes();

  public function getEndpoint($user_id);
 
  public function saveToken($user_id);

  public function publish($user_id, $message);

}
