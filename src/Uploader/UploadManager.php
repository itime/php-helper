<?php

namespace Xin\Uploader;


use http\Exception\RuntimeException;
use League\Flysystem\Filesystem;
use Xin\Capsule\Manager;
use Xin\Contracts\Uploader\Factory;
use Xin\Contracts\Uploader\Uploader;

/**
 * @mixin Uploader
 */
class UploadManager extends Manager implements Factory
{
	/**
	 * ETag 算法
	 */
	public const HASH_ETAG = 'etag';

	/**
	 * md5 算法
	 */
	public const HASH_MD5 = 'md5';

	/**
	 * Sha1 算法
	 */
	public const HASH_SHA1 = 'sha1';

	/**
	 * @var callable
	 */
	protected $filesystemResolver;

	/**
	 * @var callable
	 */
	protected $uploadProviderResolver;

	/**
	 * @param callable $filesystemResolver
	 * @param callable|null $uploadProviderResolver
	 * @param array $config
	 */
	public function __construct(callable $filesystemResolver, callable $uploadProviderResolver = null, array $config = [])
	{
		parent::__construct($config);
		$this->filesystemResolver = $filesystemResolver;
		$this->uploadProviderResolver = $uploadProviderResolver;
	}

	/**
	 * 上传文件
	 * @param string $scene
	 * @param \SplFileInfo $file
	 * @param array $options
	 * @return array
	 */
	public function file($scene, \SplFileInfo $file, array $options = [])
	{
		$uploadProvider = $this->getUploadProvider($scene);

		$hash = $this->getFileHash($file);
		$hashType = $this->getHashType();
		$hashMethod = "getBy{$hashType}";
		$info = $uploadProvider->{$hashMethod}($scene, $hash);

		if (!$info) {
			$options = $this->optimizeOptions($options);

			$path = $this->buildPath($scene, $file->getFilename(), $options);
			$result = $this->disk($scene)->put($path, file_get_contents($file->getRealPath()));

			$info = $uploadProvider->save($scene, $result);

//			$info = $this->uploader($scene)->file($scene, $file, $options);
//			$info = $uploadProvider->save($scene, $info);
		}

		return method_exists($uploadProvider, 'renderData') ? $uploadProvider->renderData($info) : $info;
	}

	/**
	 * 获取上传令牌
	 * @param string $scene
	 * @param string $filename
	 * @param array $options
	 * @return array
	 */
	public function token($scene, $filename, array $options = [])
	{
		return $this->uploader($scene)->token($scene, $filename, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function uploader($scene = null)
	{
		$scene = $this->getSceneAlias($scene);

		return $this->driver($scene);
	}

	/**
	 * 获取场景别名
	 * @param string $scene
	 * @return string
	 */
	protected function getSceneAlias($scene)
	{
		if (!$scene) {
			return $scene;
		}

		return $this->getConfig('aliases.' . $scene, $scene);
	}

	/**
	 * 获取提供者
	 * @param string $scene
	 * @return \Xin\Contracts\Uploader\UploadProvider
	 */
	public function getUploadProvider($scene)
	{
		$scene = $this->getSceneAlias($scene);
		$providerClass = $this->getDriverConfig($scene . 'provider');

		if (empty($providerClass)) {
			$providerClass = $this->getDefaultConfig('provider');
		}

		if (empty($providerClass)) {
			throw new \RuntimeException("UploadManager scene({$scene}) provider not defined.");
		}

		if ($this->uploadProviderResolver) {
			$provider = call_user_func($this->uploadProviderResolver, $providerClass, $scene);
		} elseif (method_exists($this->container, 'make')) {
			$provider = $this->container->make($providerClass);
		} elseif (method_exists($this->container, 'get')) {
			$provider = $this->container->get($providerClass);
		} else {
			throw new \RuntimeException("UploadManager scene({$scene}) provider not resolved.");
		}

		return $provider;
	}

	/**
	 * 获取Disk
	 * @param string $scene
	 * @return Filesystem
	 */
	protected function disk($scene)
	{
		$defaultDisk = $this->getDefaultConfig('disk', 'default');
		$disk = $this->getDriverConfig($scene . '.disk') ?: $defaultDisk;
		return call_user_func($this->filesystemResolver, $disk);
	}

	/**
	 * 创建默认驱动
	 * @param string $name
	 * @param array $config
	 * @return QiniuUploader
	 */
	protected function createDefaultDriver($name, $config)
	{
		$disk = $this->disk($name);

		return new QiniuUploader($disk, $config);
	}

	/**
	 * 获取默认驱动
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->getConfig('defaults.scene', 'default');
	}

	/**
	 * 设置默认驱动
	 * @param string $name
	 */
	public function setDefaultDriver($name)
	{
		$this->setConfig('defaults.scene', $name);
	}

	/**
	 * 获取驱动配置
	 * @param string $name
	 * @return array|\ArrayAccess|mixed
	 */
	public function getDriverConfig($name)
	{
		$key = 'scenes';

		return $this->getConfig($name ? "{$key}.{$name}" : $key);
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return array|\ArrayAccess|mixed
	 */
	protected function getDefaultConfig($key, $default = null)
	{
		return $this->getConfig('defaults.' . $key, $default);
	}

	/**
	 * 获取文件hash
	 * @param \SplFileInfo $file
	 * @return string
	 */
	protected function getFileHash(\SplFileInfo $file)
	{
		$hashType = $this->getHashType();
		if (self::HASH_ETAG === $hashType) {
			return Etag::sum($file->getFilename());
		}

		if (self::HASH_MD5 === $hashType) {
			return md5_file($file->getRealPath());
		}

		if (self::HASH_SHA1 === $hashType) {
			return sha1_file($file->getRealPath());
		}

		if (in_array($hashType, hash_algos(), true)) {
			return hash_file($hashType, $file, true);
		}

		throw new RuntimeException("hash_type[{$hashType}] is not support.");
	}

	/**
	 * 获取hash类型
	 * @return string
	 */
	public function getHashType()
	{
		return $this->getDefaultConfig('hash_type', 'etag');
	}

	/**
	 * 优化配置项
	 * @param array $options
	 * @return array
	 */
	protected function optimizeOptions($options)
	{
		return array_replace_recursive($this->config, $options);
	}

	/**
	 * 生成路径
	 * @param string $scene
	 * @param string $filename
	 * @param array $options
	 * @return string
	 */
	protected function buildPath($scene, $filename, array $options)
	{
		$basePath = $options['base_path'];

		return "{$basePath}/{$scene}/{$filename}";
	}
}
