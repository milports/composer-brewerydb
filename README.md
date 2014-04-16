composer-brewerydb
==================

Composer package for implementing the BreweryDB API in Laravel, using Guzzle (version 4) for retrieval and caching.

# Installation

### Add to provider section in app/config/app.php
`` 'Beerguide\Brewerydb\BrewerydbServiceProvider' ``

### Add to alias section in app/config/app.php
`` 'Brewerydb'         => 'Beerguide\Brewerydb\Client' ``

## Example usage

Place the following in your controller.

```
$query = new Brewerydb('your-api-key');
// If API is online
if ($query) {
	$params = array(
		'format' => 'json',
		'withBreweries' => 'Y',
		'p' => $page_number,
		'status' => 'verified'
	);
	$results = $query->request('beers', $params, 'GET', true);
	$number_of_pages = $results['numberOfPages'];
	$total_results = $results['totalResults'];
	$per_page = count($results['data']);

	$data = $results['data'];
	$beers = Paginator::make($data, $total_results, $per_page);
} else {
	// API is offline
}
```
