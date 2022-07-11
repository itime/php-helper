<?php

namespace Xin\Uploader;


use League\Flysystem\Filesystem;
use think\helper\Str;
use Xin\Capsule\Manager;
use Xin\Contracts\Uploader\Factory;
use Xin\Contracts\Uploader\Uploader;
use Xin\Support\Arr;

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
	 * @var callable
	 */
	protected $validateFileResolver;

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
		$config = $this->getDriverConfig($scene);
		$config = array_replace_recursive($config, $options);

		return $this->getFileInfo($scene, $file, function () use ($scene, $file, $options) {
			$this->validateFile($scene, $file, $options);

			$targetPath = $this->buildPath($scene, $file->getPathname(), $options) . "." .
				$this->getExtension(mime_content_type($file->getPathname()));

			$result = $this->uploader($scene)->file($scene, $targetPath, $file, $options);

			return $this->normalizeFileInfo($targetPath, $file, $result);
		});
	}

	/**
	 * 验证文件合法性
	 * @return void
	 */
	protected function validateFile($scene, \SplFileInfo $file, $options = [])
	{
		$config = $this->getDriverConfig($scene);
		$config = array_replace_recursive($config, $options);

		return call_user_func($this->validateFileResolver, $file, $config);
	}

	/**
	 * 获取文件信息
	 * @param string $scene
	 * @param \SplFileInfo $file
	 * @param callable $callback
	 * @return array
	 */
	protected function getFileInfo($scene, \SplFileInfo $file, $callback)
	{
		$uploadProvider = $this->getUploadProvider($scene);

		$hashType = $this->getFileHashType($scene);
		$hash = $this->getFileHash($hashType, $file->getRealPath());
		$info = $uploadProvider->retrieveByHash($scene, $hashType, $hash);

		if (!$info) {
			$result = $callback();
			$info = $uploadProvider->save($scene, $result);
		}

		return method_exists($uploadProvider, 'renderData') ? $uploadProvider->renderData($info) : $info;
	}

	/**
	 * 保存文件信息
	 * @param string $scene
	 * @param array $notifyData
	 * @return array
	 */
	public function saveFileInfo($scene, $notifyData)
	{
		$uploadProvider = $this->getUploadProvider($scene);

		$fileInfo = $this->uploader($scene)->transformNotifyData($notifyData);

		$hashType = $this->getFileHashType($scene);
		$hash = $fileInfo['hash'];

		$info = $uploadProvider->retrieveByHash($scene, $hashType, $hash);
		if (!$info) {
			$info = $uploadProvider->save($scene, $fileInfo);
		}

		return method_exists($uploadProvider, 'renderData') ? $uploadProvider->renderData($info) : $info;
	}

	/**
	 * 获取上传令牌
	 * @param string $scene
	 * @param array $file
	 * @param array $options
	 * @return array
	 */
	public function token($scene, array $file, array $options = [])
	{
		$config = $this->getDriverConfig($scene);
		$config = array_replace_recursive($config, $options);

		$mimeType = $file['mime'] ?? $file['type'] ?? '';
		$extension = $this->getExtension($mimeType);
		$filename = Str::random(6);

		$targetPath = $this->buildPath($scene, $filename, $config) . '.' . $extension;

		return $this->uploader($scene)->token($scene, $targetPath, $config);
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
		$sceneConfig = $this->getDriverConfig($scene);
		$providerClass = Arr::get($sceneConfig, 'provider', $this->getDefaultConfig('provider'));

		if (empty($providerClass)) {
			throw new \RuntimeException("UploadManager scene({$scene}) provider not defined.");
		}

		if ($this->uploadProviderResolver) {
			$provider = call_user_func($this->uploadProviderResolver, $providerClass, $scene, $sceneConfig);
		} elseif (method_exists($this->container, 'make')) {
			$provider = $this->container->make($providerClass, [
				'config' => $sceneConfig
			]);
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
	public function getDriverConfig($name, $default = null)
	{
		$key = 'scenes';

		return $this->getConfig($name ? "{$key}.{$name}" : $key, $default);
	}

	/**
	 * 获取默认配置
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getDefaultConfig($key, $default = null)
	{
		return $this->getConfig('defaults.' . $key, $default);
	}

	/**
	 * 获取hash类型
	 * @return string
	 */
	public function getFileHashType($scene)
	{
		return $this->getDriverConfig($scene . ".hash_type") ?: $this->getDefaultConfig('hash_type', 'etag');
	}

	/**
	 * 获取文件hash
	 * @param string $filepath
	 * @return string
	 */
	public function getFileHash($hashType, $filepath)
	{
		if (self::HASH_ETAG === $hashType) {
			return Etag::sum($filepath);
		}

		if (self::HASH_MD5 === $hashType) {
			return md5_file($filepath);
		}

		if (self::HASH_SHA1 === $hashType) {
			return sha1_file($filepath);
		}

		if (in_array($hashType, hash_algos(), true)) {
			return hash_file($hashType, $filepath, true);
		}

		throw new \RuntimeException("hash_type[{$hashType}] is not support.");
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
		$path = '';

//		$hash = hash_file('md5', $filename);
//		$hashName = substr($hash, 0, 2) . DIRECTORY_SEPARATOR . substr($hash, 2);
		$hashName = date('Ymd') . '/' . md5(microtime(true) . $filename);

		$basePath = $options['base_path'] ?? '';
		if ($basePath) {
			$path .= "{$basePath}/";
		}

		return $path . "{$scene}/{$hashName}";
	}

	/**
	 * 根据mimeType获取文件扩展名
	 * @param string $mimeType
	 * @return string
	 */
	protected function getExtension($mimeType)
	{
		$mimeMaps = $this->getMimeMaps();
		if (!isset($mimeMaps[$mimeType])) {
			throw new \LogicException("文件类型不允许上传！");
		}

		return $mimeMaps[$mimeType];
	}

	/**
	 * 获取mime 映射后缀名
	 * @return array
	 */
	public function getMimeMaps()
	{
		return $this->getConfig('ext_maps', []);
	}

	/**
	 * @param string $targetPath
	 * @param \SplFileInfo $file
	 * @param array $result
	 * @return array
	 */
	protected function normalizeFileInfo($targetPath, \SplFileInfo $file, $result)
	{
		return array_merge([
			'path' => $targetPath,
			'filename' => $file->getFilename(),
			'size' => $file->getSize(),
			'extension' => $file->getExtension(),
			'mime_type' => mime_content_type($file->getRealPath()),
			'md5' => md5_file($file->getRealPath()),
			'sha1' => sha1_file($file->getRealPath()),
		], $result);
	}

	/**
	 * 使用回调验证器
	 * @param callable $callback
	 * @return void
	 */
	public function validateFileUsing(callable $callback)
	{
		$this->validateFileResolver = $callback;
	}
}
