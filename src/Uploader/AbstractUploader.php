<?php

namespace Xin\Uploader;

use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use Xin\Capsule\Service;
use Xin\Contracts\Uploader\Uploader as UploaderContract;

abstract class AbstractUploader extends Service implements UploaderContract
{
	/**
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * @param Filesystem $filesystem
	 * @param array $config
	 */
	public function __construct(Filesystem $filesystem, array $config)
	{
		parent::__construct(array_merge_recursive([
			'base_path' => '',
			'size' => 0,
			'extensions' => '',
			'mimes' => '',
			'cdn' => '',
			'user_data' => [],
		], $config));

		$this->filesystem = $filesystem;
	}

	/**
	 * 优化配置项
	 * @param array $options
	 * @return array
	 */
	protected function optimizeOptions($options)
	{
		return array_merge_recursive($this->config, $options);
	}

	/**
	 * Concatenate a path to a URL.
	 *
	 * @param string $url
	 * @param string $path
	 * @return string
	 */
	protected function concatPathToUrl($url, $path)
	{
		return rtrim($url, '/') . '/' . ltrim($path, '/');
	}

	/**
	 * @param \League\Flysystem\Config $config
	 * @return string
	 */
	protected function carryUrl(Config $config)
	{
		if ($config->has('url')) {
			return $config->get('url');
		}

		if ($config->has('domain')) {
			return $config->get('domain');
		}

		if ($config->has('cdn')) {
			return $config->get('cdn');
		}

		return '';
	}
}