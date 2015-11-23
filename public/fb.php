<?php
  require '../vendor/autoload.php';

  $client = new \GuzzleHttp\Client();
  $data = array(
    'client_id'     => 1,
    'client_secret' => 'gKYG75sw',
    'grant_type'    => 'client_credentials'
  );
  $guzzleResponse = $client->post('http://home.bunduc.ro/api/v1/auth/request-token', ['json' => $data]);
  $response = json_decode($guzzleResponse->getBody());

  $client = new \GuzzleHttp\Client();
  $guzzleResponse = $client->get('http://home.bunduc.ro/api/v1/fb/getEndpoint', [
    'headers' => [
      'Authorization' => $response->accessToken
    ]
  ]);
  // echo $res->getBody();

  $response_data = json_decode($guzzleResponse->getBody());
  echo $response->accessToken . " | ";
  echo "<a href=\"{$response_data->data->login_url}\">Login</a>";
?>