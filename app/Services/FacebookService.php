<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Socialite;
use Config;
use Session;

class FacebookService implements AbstractService {
  const CALLBACK = '/api/v1/fb/saveToken';
  const TOKEN_TTL = 5184000; // 60 days

  protected $serviceRepository;
  protected $request;
  protected $service;

  function __construct(Request $request, ServiceRepository $repository) {
    $this->serviceRepository = $repository;
    $this->request = $request;
    $this->service = new \Facebook\Facebook([
      'app_id' => Config::get('services.facebook.client_id'),
      'app_secret' => Config::get('services.facebook.client_secret'),
      'default_graph_version' => Config::get('services.facebook.default_graph_version')
    ]);
  }

  protected function prepareRequest($user_id) {
    $userProvider = $this->serviceRepository->getUserProvider((string) $user_id);

    if (empty($userProvider['facebook_access_token'])) {
      throw new Exception('Invalid access token');
    }

    $this->service->setDefaultAccessToken($userProvider['facebook_access_token']);
  }

  public static function scopes() {
    return ['publish_actions', 'publish_pages', 'manage_pages', 'user_posts', 'user_about_me', 'user_likes', 'user_friends'];
  }

  public function getEndpoint($user_id) {
    Config::set('services.facebook.redirect', Config::get('services.facebook.redirect') . '/' . $user_id);
    $redirectUrl = Socialite::driver('facebook')->scopes(FacebookService::scopes())->redirect()->getTargetUrl();

    return $redirectUrl;
  }

  public function saveToken($user_id) {
    Config::set('services.facebook.redirect', Config::get('services.facebook.redirect') . '/' . $user_id);
    Session::set('state', $this->request->input('state'));

    $user = Socialite::driver('facebook')->user();

    $this->serviceRepository->saveToken($user_id, ['token' => $user->token, 'expires_at' => FacebookService::TOKEN_TTL + time()]);

    return true;
  }

  public function publish($user_id, $message) {
    $this->prepareRequest($user_id);

    $response = $this->service->post('/me/feed', ['message' => $message]);
    $graphObject = $response->getDecodedBody();

    return $graphObject['id'];
  }
}
