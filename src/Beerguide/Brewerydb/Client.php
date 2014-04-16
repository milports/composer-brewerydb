<?php namespace Beerguide\Brewerydb;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Client {

	/**
	 * Base URL for the Brewerydb API
	 *
	 * @var string
	 */
	const BASE_URL = 'http://api.brewerydb.com/v2';

	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';

	/**
	 * API key
	 *
	 * @var string
	 */
	protected $_apiKey = '';
	protected $_url = '';
	protected $_statusCode;


	/**
	 * Stores the last parsed response from the server
	 *
	 * @var stdClass
	 */
	protected $_lastParsedResponse = null;

	/**
	 * Stores the last raw response from the server
	 *
	 * @var string
	 */
	protected $_lastRawResponse = null;

	/**
	 * Stores the last requested URI
	 *
	 * @var string
	 */
	protected $_lastRequestUri = null;

	/**
	 * Constructor
	 *
	 * @param string $apiKey Brewerydb API key
	 */
	public function __construct($apiKey, $url = self::BASE_URL)
	{
		$this->_apiKey = (string) $apiKey;
		$this->_url = (string) $url;

		// Check availability of API
		if ($this->heartbeat() == 200) {
            // API online
		} else {
		    // API offline
		}
	}

	/**
	 * Heartbeat - API Availability
	 *
	 * @return boolean
	 */
	public function heartbeat()
	{
        $client = new Client(['base_url' => $this->_url]);
        $available = $client->get('/heartbeat');

		return $available->getStatusCode();
	}

	/**
	 * Sends a request using curl to the required endpoint
	 *
	 * @param string $endpoint The BreweryDb endpoint to use
	 * @param array $args key value array of arguments
	 *
	 * @return array
	 */
	public function request($endpoint, $args = array(), $transferType = self::GET, $cache = false)
	{
		$this->_apiKey = $this->_apiKey;
		$this->_url = $this->_url;

		$this->_lastRequestUri = null;
		$this->_lastRawResponse = null;
		$this->_lastParsedResponse = null;

		if ($transferType == self::GET) {
			$client = new Client(['base_url'] => $this->_url]);
			if ($cache == true) {
                /*
				$cachePlugin = new CachePlugin(array(
					'storage' => new DefaultCacheStorage(
						new DoctrineCacheAdapter(
							new FilesystemCache(storage_path().'/cache')
						)
					)
				));

				// Add the cache plugin to the client object
				$client->addSubscriber($cachePlugin);
                */
			}

			// Check if format is set, otherwise set to json as default
			if (! isset($args['format']) || ($args['format'] != 'json' || $args['format'] != 'xml' || $args['format'] != 'php')) {
				$args['format'] = 'json';
			}

			$request = $client->get(array('{+path}{/segments}{?key,data*}', array(
				'path'            => $endpoint,
				'key'             => $this->_apiKey,
				'data'            => $args
			)));

			// Attempt connection and throw error if bad HTTP response is received
			try {
				$this->_lastRawResponse = $request->send();
				$this->_statusCode = $this->_lastRawResponse->getStatusCode();

				if ($this->_statusCode == 200) {
					if ($args['format'] == 'php') {
						$this->_lastParsedResponse = $this->_lastRawResponse->php();
					} elseif ($args['format'] == 'json') {
						$this->_lastParsedResponse = $this->_lastRawResponse->json();
					} else {
						$this->_lastParsedResponse = $this->_lastRawResponse->xml();
					}

					return $this->getLastParsedResponse();
				}
			} catch (RequestException $e) {
				echo 'Uh oh! ' . $e->getMessage();
				echo 'HTTP request URL: ' . $e->getRequest()->getUrl() . "\n";
				echo 'HTTP request: ' . $e->getRequest() . "\n";
				echo 'HTTP response status: ' . $e->getResponse()->getStatusCode() . "\n";
				echo 'HTTP response: ' . $e->getResponse() . "\n";

				return false;
			}
		} else if ($transferType == self::POST) {

			$client = new Client(['base_url'] => $this->_url]);
			$request = $client->post(array('{+path}{?key,data*}', array(
				'path'            => $endpoint,
				'key'             => $this->_apiKey,
				'data'            => $args
			)));

			// Attempt connection and throw error if bad HTTP response is received
			try {
				$this->_lastRawResponse = $request->send();
				$this->_statusCode = $this->_lastRawResponse->getStatusCode();

				if ($this->_statusCode == 201) {
					$this->_lastParsedResponse = $this->_lastRawResponse->json();

					return $this->getLastParsedResponse();
				}
			} catch (RequestException $e) {
				echo 'Uh oh! ' . $e->getMessage();
				echo 'HTTP request URL: ' . $e->getRequest()->getUrl() . "\n";
				echo 'HTTP request: ' . $e->getRequest() . "\n";
				echo 'HTTP response status: ' . $e->getResponse()->getStatusCode() . "\n";
				echo 'HTTP response: ' . $e->getResponse() . "\n";

				return false;
			}
		} else if ($transferType == self::PUT) {
			return false;
			/*
			$this->_lastRequestUri = $this->_url . '/' . $endpoint . '/';

			$file = tmpfile();
			$string = http_build_query($args);
			fwrite($file, $string);
			fseek($file, 0);

			curl_setopt($ch, CURLOPT_INFILE, $file);
			curl_setopt($ch, CURLOPT_INFILESIZE, strlen($string));
			curl_setopt($ch, CURLOPT_PUT, 4);
			curl_setopt($ch, CURLOPT_URL, $this->_lastRequestUri);
			curl_setopt($ch, CURLOPT_HTTPHEADER, Array('Expect: '));
			*/

		} else if ($transferType == self::DELETE) {
			return false;
			/*
			$this->_lastRequestUri = $this->_url . '/' . $endpoint . '/';

			curl_setopt($ch, CURLOPT_URL, $this->_lastRequestUri);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::DELETE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
			*/
		}
	}

	/**
	 * Gets the last parsed response from the service
	 *
	 * @return null|array
	 */
	public function getLastParsedResponse()
	{
		return $this->_lastParsedResponse;
	}

	/**
	 * Gets the last raw response from the service
	 *
	 * @return null|json string|xml string
	 */
	public function getLastRawResponse()
	{
		return $this->_lastRawResponse;
	}

	/**
	 * Gets the last request URI sent to the service
	 *
	 * @return null|string
	 */
	public function getLastRequestUri()
	{
		return $this->_lastRequestUri;
	}

}
