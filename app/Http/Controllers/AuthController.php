<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{

  public function __construct() {
  }

  public function attemptLogin(Request $request)
  {
    $data = $request->all();
    $config = app()->make('config');

    if ($data['client_id'] == $config->get('secrets.client_id') && !array_key_exists('client_secret', $data)) {
      $data['client_secret'] = $config->get('secrets.client_secret');
    }

    return $this->proxy('client_credentials', $data);
  }

  private function proxy($grantType, array $data = []) 
  {
    try {
      $config = app()->make('config');

      $data = array_merge([
        'client_id'     => (int) $data['client_id'],
        'client_secret' => $data['client_secret'],
        'grant_type'    => $grantType
      ], $data);

      $client = new \GuzzleHttp\Client();
      $guzzleResponse = $client->post(sprintf('%s/oauth/access-token', $config->get('app.url')), [
        'json' => $data
      ]);
    } catch(\GuzzleHttp\Exception\BadResponseException $e) {
      $guzzleResponse = $e->getResponse();
    }

    $response = json_decode($guzzleResponse->getBody());

    if (property_exists($response, "access_token")) {
      $response = [
        'accessToken'            => $response->access_token,
        'accessTokenExpiration'  => $response->expires_in
        // 'refresh_token'          => $response->refresh_token
      ];
    }

    $response = response()->json($response);
    $response->setStatusCode($guzzleResponse->getStatusCode());

    $headers = $guzzleResponse->getHeaders();
    foreach($headers as $headerType => $headerValue) {
      $response->header($headerType, $headerValue);
    }

    return $response;
  }

}
