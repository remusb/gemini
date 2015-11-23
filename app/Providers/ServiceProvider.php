<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
  protected $defer = true;

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->when('App\Services\FacebookService')
      ->needs('App\Repositories\ServiceRepository')
      ->give('App\Repositories\FacebookServiceRepository');

    $this->app->when('App\Services\TwitterService')
      ->needs('App\Repositories\ServiceRepository')
      ->give('App\Repositories\TwitterServiceRepository');
  }

  public function provides()
  {
    return ['App\Repositories\FacebookServiceRepository', 'App\Repositories\TwitterServiceRepository', 'App\Repositories\ServiceRepository'];
  }
}
