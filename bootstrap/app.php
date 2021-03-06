<?php

require_once __DIR__.'/../vendor/autoload.php';

Dotenv::load(__DIR__.'/../');

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->withFacades();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    Illuminate\Session\Middleware\StartSession::class,
    Illuminate\View\Middleware\ShareErrorsFromSession::class,
    LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class
]);

$app->routeMiddleware([
    'check-authorization-params' => LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware::class,
    'oauth' => LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
    'oauth-owner' => LucaDegasperi\OAuth2Server\Middleware\OAuthOwnerMiddleware::class,
]);

// $app->routeMiddleware([

// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\ServiceProvider::class);
$app->register(Jenssegers\Mongodb\MongodbServiceProvider::class);
$app->register(Laravel\Socialite\SocialiteServiceProvider::class);
$app->register(\LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class);
$app->register(\LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider::class);

$app->withEloquent();

class_alias(Laravel\Socialite\Facades\Socialite::class, 'Socialite');
class_alias(Illuminate\Support\Facades\Config::class, 'Config');

$app->configure('services');
$app->configure('app');
$app->configure('secrets');

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../app/Http/routes.php';
});

return $app;
