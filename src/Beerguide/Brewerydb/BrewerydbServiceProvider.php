<?php namespace Beerguide\Brewerydb;

use Illuminate\Support\ServiceProvider;

class BrewerydbServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('beerguide/brewerydb');

		// Bring the application container instance into the local scope so we can
		// import it into the filters scope.
		$app = $this->app;

		$this->app->finish(function() use ($app)
		{
			if ($app['config']->get('apikey' == ''))
			{
				// Error, no apikey configured
			}
		});

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['brewerydb'] = $this->app->share(function($app)
		{
			return new Brewerydb;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('brewerydb');
	}

}