<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $app->get('/', function () use ($app) {
//     return $app->welcome();
// });

// $app->post('refresh-token', function() use($app) {
//   $credentials = app()->make('request')->input("credentials");

//   return $app->make('App\Http\Controllers\AuthController')->attemptRefresh();
// });

$app->group(['prefix' => 'api/v1/auth', 'namespace' => 'App\Http\Controllers'], function($app)
{
  $app->post('request-token', 'AuthController@attemptLogin');
  $app->get('request-token', 'AuthController@attemptLogin');
});

$app->post('oauth/access-token', function() use($app) {
  return response()->json($app->make('oauth2-server.authorizer')->issueAccessToken());
});

$app->group(['prefix' => 'api/v1/{service}','namespace' => 'App\Http\Controllers'], function($app)
{
  $app->get('saveToken/{user_id}', 'ServiceController@saveToken');
});

$app->group(['prefix' => 'api/v1/{service}','namespace' => 'App\Http\Controllers', 'middleware' => 'oauth'], function($app)
{
  $app->get('getEndpoint', 'ServiceController@getEndpoint');
  $app->get('publish/{message}', 'ServiceController@publish');
});

