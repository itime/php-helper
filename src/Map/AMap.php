<?php

namespace Xin\Map;

use Xin\Capsule\WithConfig;
use Xin\Http\Client;

class AMap
{
	use WithConfig;

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var string
	 */
	protected $baseUri = "https://restapi.amap.com";

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;

		$httpConfig = $config['http'] ?? [];
		$httpConfig['base_uri'] = $this->baseUri;
		$this->client = new Client($httpConfig);
	}

	/**
	 * 经纬度转地址
	 * @param string $lng
	 * @param string $lat
	 * @return array
	 */
	public function regionByLocation($lng, $lat)
	{
		$result = $this->client->get("/v3/geocode/regeo", [
			'key' => $this->getKey(),
			'location' => "{$lng},{$lat}"
		])->json();
		if (!$result) {
			return null;
		}

		return isset($result['regeocode']) ? $result['regeocode'] : null;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->getConfig('key', '');
	}


}