<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Foundation\Filesystem;

use Xin\Filesystem\FilesystemInterface;
use Xin\Support\Str;

class StorageBuilder {

	/**
	 * @var string
	 */
	protected $appId;

	/**
	 * @var string
	 */
	protected $category;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @var \League\Flysystem\FilesystemInterface
	 */
	protected $filesystem;

	/**
	 * @var bool
	 */
	protected static $validateAppId = false;

	/**
	 * @var callable
	 */
	protected static $defaultFilesystemCreator = null;

	/**
	 * @var \Exception|\Throwable
	 */
	protected $exception;

	/**
	 * @return string
	 */
	public function getAppId() {
		return $this->appId;
	}

	/**
	 * @param string $appId
	 * @return $this
	 */
	public function setAppId($appId) {
		$this->appId = $appId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 * @return $this
	 */
	public function setPath($path) {
		$this->path = $path;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param string $category
	 * @return $this
	 */
	public function setCategory($category) {
		$this->category = $category;

		return $this;
	}

	/**
	 * @return \League\Flysystem\FilesystemInterface
	 */
	public function getFilesystem() {
		return $this->filesystem;
	}

	/**
	 * @param \League\Flysystem\FilesystemInterface $filesystem
	 * @return $this
	 */
	public function setFilesystem(\League\Flysystem\FilesystemInterface $filesystem) {
		$this->filesystem = $filesystem;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * @param string $filename
	 * @return $this
	 */
	public function setFilename($filename) {
		$this->filename = $filename;

		return $this;
	}

	/**
	 * @param string $suffix
	 * @param int    $length
	 * @return $this
	 */
	public function generateFilename($suffix, $length = 16) {
		$this->filename = Str::random($length) . ($suffix ? '.' . $suffix : '');

		return $this;
	}

	/**
	 * @return \Exception|\Throwable
	 */
	public function getException() {
		return $this->exception;
	}

	/**
	 * @return \League\Flysystem\FilesystemInterface
	 */
	public function filesystem() {
		if (!$this->filesystem) {
			$this->filesystem = $this->createFilesystem();
		}

		return $this->filesystem;
	}

	/**
	 * 创建文件系统实现器
	 *
	 * @return \Xin\Filesystem\FilesystemInterface
	 */
	protected function createFilesystem() {
		if (!self::$defaultFilesystemCreator) {
			throw new \LogicException("未定义Filesystem实现器！");
		}

		$filesystem = call_user_func(self::$defaultFilesystemCreator, $this);
		if (!$filesystem instanceof FilesystemInterface) {
			// throw new \LogicException("filesystem creator must return ".FilesystemInterface::class." type.");
		}

		return $filesystem;
	}

	/**
	 * @return string
	 */
	public function buildTargetPath() {
		if (self::$validateAppId && !$this->appId) {
			throw new \LogicException('appId invalid.');
		}

		if (!$this->category) {
			throw new \LogicException('category invalid.');
		}

		if (!$this->path) {
			throw new \LogicException('path invalid.');
		}

		if (!$this->filename) {
			throw new \LogicException('filename invalid.');
		}

		$date = date('Ymd');

		return "{$this->appId}/{$this->category}/{$this->path}/{$date}/{$this->filename}";
	}

	/**
	 * 上传本地文件
	 *
	 * @param string $localFilepath
	 * @param array  $options
	 * @return string
	 */
	public function upload($localFilepath, $options = []) {
		$stream = fopen($localFilepath, 'r');

		$result = $this->uploadStream($stream, $options);

		if (is_resource($stream)) {
			fclose($stream);
		}

		return $result;
	}

	/**
	 * 上传本地文件
	 *
	 * @param \SplFileInfo $fileInfo
	 * @param array        $options
	 * @return string
	 */
	public function uploadFile(\SplFileInfo $fileInfo, $options = []) {
		return $this->upload($fileInfo->getRealPath(), $options);
	}

	/**
	 * 上传文件字符串
	 *
	 * @param string $content
	 * @param array  $options
	 */
	public function uploadContent($content, $options = []) {
		$target = $this->buildTargetPath();

		try {
			$result = $this->filesystem()->put($target, $content, $options);

			return $result ? $target : null;
		} catch (\Throwable $e) {
			$this->exception = $e;
		}

		return null;
	}

	/**
	 * 上传文件流
	 *
	 * @param resource $stream
	 * @param array    $options
	 * @return string
	 */
	public function uploadStream($stream, $options = []) {
		$target = $this->buildTargetPath();
		try {
			$result = $this->filesystem()->putStream($target, $stream, $options);

			return $result ? $target : null;
		} catch (\Throwable $e) {
			$this->exception = $e;
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public static function isValidateAppId() {
		return self::$validateAppId;
	}

	/**
	 * @param bool $validateAppId
	 */
	public static function setValidateAppId($validateAppId) {
		self::$validateAppId = $validateAppId;
	}

	/**
	 * @return callable
	 */
	public static function getDefaultFilesystemCreator() {
		return self::$defaultFilesystemCreator;
	}

	/**
	 * @param callable $defaultFilesystemCreator
	 */
	public static function setDefaultFilesystemCreator($defaultFilesystemCreator) {
		self::$defaultFilesystemCreator = $defaultFilesystemCreator;
	}

	/**
	 * 构建实例
	 *
	 * @param string $category
	 * @param string $path
	 * @param string $filename
	 */
	public static function builder($category, $path, $filename = '') {
		$self = new static();
		$self->setCategory($category);
		$self->setPath($path);
		$self->setFilename($filename);

		return $self;
	}

}
