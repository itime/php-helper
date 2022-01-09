<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;

trait HasHttpRequests {

	/**
	 * @var \GuzzleHttp\ClientInterface
	 */
	protected $httpClient;

	/**
	 * @var array
	 */
	protected $middlewares = [];

	/**
	 * @var \GuzzleHttp\HandlerStack
	 */
	protected $handlerStack;

	/**
	 * @var array
	 */
	protected static $defaults = [
		'curl' => [
			CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
		],
	];

	/**
	 * Set guzzle default settings.
	 *
	 * @param array $defaults
	 */
	public static function setDefaultOptions($defaults = []) {
		static::$defaults = $defaults;
	}

	/**
	 * Return current guzzle default settings.
	 */
	public static function getDefaultOptions() {
		return static::$defaults;
	}

	/**
	 * Set GuzzleHttp\Client.
	 *
	 * @return $this
	 */
	public function setHttpClient(ClientInterface $httpClient) {
		$this->httpClient = $httpClient;

		return $this;
	}

	/**
	 * Return GuzzleHttp\ClientInterface instance.
	 */
	public function getHttpClient(): ClientInterface {
		if (!($this->httpClient instanceof ClientInterface)) {
			if (property_exists($this, 'app') && $this->app['http_client']) {
				$this->httpClient = $this->app['http_client'];
			} else {
				$this->httpClient = new Client(['handler' => HandlerStack::create($this->getGuzzleHandler())]);
			}
		}

		return $this->httpClient;
	}

	/**
	 * Add a middleware.
	 *
	 * @param callable    $middleware
	 * @param string|null $name
	 *
	 * @return $this
	 */
	public function pushMiddleware(callable $middleware, string $name = null) {
		if (!is_null($name)) {
			$this->middlewares[$name] = $middleware;
		} else {
			array_push($this->middlewares, $middleware);
		}

		return $this;
	}

	/**
	 * Return all middlewares.
	 */
	public function getMiddlewares() {
		return $this->middlewares;
	}

	/**
	 * Register Guzzle middlewares.
	 */
	protected function registerHttpMiddlewares() {
		// log
		// $this->pushMiddleware($this->logMiddleware(), 'log');
	}

	/**
	 * Log the request.
	 *
	 * @return \Closure
	 */
	protected function logMiddleware() {
		// $formatter = new MessageFormatter($this->app['config']['http.log_template'] ?? MessageFormatter::DEBUG);
		//
		// return Middleware::log($this->app['logger'], $formatter, LogLevel::DEBUG);
	}

	/**
	 * Make a request.
	 *
	 * @param string $url
	 * @param string $method
	 * @param array  $options
	 * @return Response
	 */
	public function request($url, $method = 'GET', $options = []): Response {
		$this->registerHttpMiddlewares();

		$method = strtoupper($method);

		$options = array_merge(self::$defaults, $options, ['handler' => $this->getHandlerStack()]);

		$options = $this->fixJsonIssue($options);

		if (property_exists($this, 'baseUri') && !is_null($this->baseUri)) {
			$options['base_uri'] = $this->baseUri;
		}

		try {
			$response = $this->getHttpClient()->request($method, $url, $options);
			$response->getBody()->rewind();
		} catch (GuzzleException $e) {
			return new Response($e->getResponse(), $e);
		}

		return new Response($response);
	}

	/**
	 * GET request.
	 *
	 * @param string $url
	 * @param array  $query
	 * @return \Psr\Http\Message\ResponseInterface|Response
	 */
	public function httpGet($url, array $query = []) {
		return $this->request($url, 'GET', ['query' => $query]);
	}

	/**
	 * POST request.
	 *
	 * @param string $url
	 * @param array  $data
	 * @return \Psr\Http\Message\ResponseInterface|Response
	 */
	public function httpPost($url, array $data = []) {
		return $this->request($url, 'POST', ['form_params' => $data]);
	}

	/**
	 * JSON request.
	 *
	 * @param string $url
	 * @param array  $data
	 * @param array  $query
	 * @return \Psr\Http\Message\ResponseInterface|Response
	 */
	public function httpPostJson($url, array $data = [], array $query = []) {
		return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
	}

	/**
	 * Upload file.
	 *
	 * @param string $url
	 * @param array  $files
	 * @param array  $form
	 * @param array  $query
	 * @return \Psr\Http\Message\ResponseInterface|Response
	 */
	public function httpUpload($url, array $files = [], array $form = [], array $query = []) {
		$multipart = [];

		foreach ($files as $name => $path) {
			if (is_array($path)) {
				$multipart[] = [
					'name' => $name,
					'contents' => fopen($path['path'], 'r'),
					'filename' => $path['filename'],
				];
			} else {
				$multipart[] = [
					'name' => $name,
					'contents' => fopen($path, 'r'),
				];
			}
		}

		foreach ($form as $name => $contents) {
			$multipart[] = compact('name', 'contents');
		}

		return $this->request($url, 'POST', ['query' => $query, 'multipart' => $multipart, 'connect_timeout' => 30, 'timeout' => 30, 'read_timeout' => 30]);
	}

	/**
	 * @return $this
	 */
	public function setHandlerStack(HandlerStack $handlerStack) {
		$this->handlerStack = $handlerStack;

		return $this;
	}

	/**
	 * Build a handler stack.
	 */
	public function getHandlerStack(): HandlerStack {
		if ($this->handlerStack) {
			return $this->handlerStack;
		}

		$this->handlerStack = HandlerStack::create($this->getGuzzleHandler());

		foreach ($this->middlewares as $name => $middleware) {
			$this->handlerStack->push($middleware, $name);
		}

		return $this->handlerStack;
	}

	/**
	 * @param array $options
	 * @return array
	 */
	protected function fixJsonIssue(array $options) {
		if (isset($options['json']) && is_array($options['json'])) {
			$options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);

			if (empty($options['json'])) {
				$options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_FORCE_OBJECT);
			} else {
				$options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_UNESCAPED_UNICODE);
			}

			unset($options['json']);
		}

		return $options;
	}

	/**
	 * Get guzzle handler.
	 *
	 * @return callable
	 */
	protected function getGuzzleHandler() {
		if (property_exists($this, 'app') && isset($this->app['guzzle_handler'])) {
			return is_string($handler = $this->app->raw('guzzle_handler'))
				? new $handler()
				: $handler;
		}

		return \GuzzleHttp\choose_handler();
	}

}
