<?php

namespace Beerguide\Brewerydb;

use Illuminate\Support\ServiceProvider;

class BrewerydbServiceProvider extends ServiceProvider {

	/**
	 * Name of the Laravel package.
	 *
	 * @var string
	 */
	private static $_packageName = 'brewerydb';

	/**
	 * Returns the name of the package.
	 *
	 * @return string
	 */
	public static function getPackageName() {
		return self::$_packageName;
	}

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	public function boot() {

	}
	public function register() {
		$this->app[self::$_packageName] = $this->app->share(function($app) {
			return new Menu($app['view'], $app['config']);
		});
	}

	public function provides() {
		return [self::$_packageName];
	}

}
