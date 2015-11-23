<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use App\Repositories\UserRepository;

use App\Services\AbstractService;
use Session;

class ServiceController extends BaseController
{
  protected $service;
  protected $userRepository;

  public function __construct(AbstractService $service, UserRepository $userRepository) {
    $this->service = $service;
    $this->userRepository = $userRepository;
  }

  protected function getUserId() {
    $client_id = Authorizer::getResourceOwnerId();
    $user = $this->userRepository->getUserFromOAuthClient($client_id);

    return $user['_id'];
  }

  public function getEndpoint($service) {
    $response = ['status' => false, 'message' => '', 'data' => []];
    $user_id = $this->getUserId();

    try {
      $response['data']['login_url'] = $this->service->getEndpoint($user_id);
      $response['data']['state'] = Session::get('state', '');

      if (!empty($response['data']['login_url'])) {
        $response['status'] = true;
      }
    } catch (\Exception $ex) {
      $response['status'] = false;
      $response['message'] = $ex->getMessage();
    }

    return $response;
  }

  public function saveToken($service, $user_id = null)
  {
    $response = ['status' => false, 'message' => '', 'data' => []];

    try {
      $response['status'] = $this->service->saveToken($user_id);
    } catch (\Exception $ex) {
      $response['status'] = false;
      $response['message'] = $ex->getMessage();
    }

    return $response;
  }

  public function publish($service, $message)
  {
    $response = ['status' => false, 'message' => '', 'data' => []];
    $user_id = $this->getUserId();

    try {
      $response['data']['id'] = $this->service->publish($user_id, $message);
      $response['data']['message'] = $message;

      if (!empty($response['data']['id'])) {
        $response['status'] = true;
      }
    } catch (\Exception $ex) {
      $response['status'] = false;
      $response['message'] = $ex->getMessage();
    }

    return $response;
  }
}
