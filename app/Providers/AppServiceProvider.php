<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind('App\Services\AbstractService', function()
    {
      $request = $this->app->make(Request::class);
      $service = $request->route()[2]['service'];

      switch($service) {
        case 'fb':
          return $this->app->make('App\Services\FacebookService');
        case 'tw':
          return $this->app->make('App\Services\TwitterService');
        break;
      }
    });
  }
}
