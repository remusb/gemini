<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Config;
use Session;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterService implements AbstractService {
  const TOKEN_TTL = 5184000; // 60 days

  protected $serviceRepository;
  protected $request;
  protected $service;

  function __construct(Request $request, ServiceRepository $repository) {
    $this->serviceRepository = $repository;
    $this->request = $request;

    $this->service = new TwitterOAuth(Config::get('services.twitter.client_id'), Config::get('services.twitter.client_secret'));
  }

  public static function scopes() {
    return [];
  }

  protected function getRequestToken($user_id) {
    $request_token = $this->service->oauth('oauth/request_token', array('oauth_callback' => Config::get('services.twitter.redirect') . '/' . $user_id));

    if ($request_token['oauth_callback_confirmed'] !== "true") {
      throw new Exception("Callback url not confirmed");
    }

    return $request_token;
  }

  protected function getAccessToken($oauth_token, $oauth_verifier) {
    $data_token = $this->service->oauth('oauth/access_token', array('oauth_verifier' => $oauth_verifier, 'oauth_token' => $oauth_token));

    if (!array_key_exists('oauth_token', $data_token) || !array_key_exists('oauth_token_secret', $data_token)) {
      throw new Exception("OAuth token not confirmed");
    }

    return $data_token;
  }

  protected function prepareRequest($user_id) {
    $userProvider = $this->serviceRepository->getUserProvider($user_id);

    if (empty($userProvider['oauth_token']) || empty($userProvider['oauth_token_secret'])) {
      throw new \Exception('Invalid access token');
    }

    $this->service->setOauthToken($userProvider['oauth_token'], $userProvider['oauth_token_secret']);
  }

  public function getEndpoint($user_id) {
    $token = $this->getRequestToken($user_id);
    $redirectUrl = $this->service->url('oauth/authenticate', array('oauth_token' => $token['oauth_token']));

    return $redirectUrl;
  }

  public function saveToken($user_id) {
    if (empty($this->request->input('oauth_token')) || empty ($this->request['oauth_verifier'])) {
      throw new Exception('Token validation failed');
    }
    $request_token = $this->request->input('oauth_token');
    $request_token_verifier = $this->request->input('oauth_verifier');

    $access_token = $this->getAccessToken($request_token, $request_token_verifier);

    $this->serviceRepository->saveToken($user_id, ['request_token' => $request_token, 'request_token_verifier' => $request_token_verifier, 
      'oauth_token' => $access_token['oauth_token'], 'oauth_token_secret' => $access_token['oauth_token_secret'], 
      'expires_at' => TwitterService::TOKEN_TTL + time()]
    );

    return true;
  }

  public function publish($user_id, $message) {
    $this->prepareRequest($user_id);

    $response = $this->service->post("statuses/update", array("status" => $message));
    var_dump($response);
    die;

    return $response->id_str;
  }
}
