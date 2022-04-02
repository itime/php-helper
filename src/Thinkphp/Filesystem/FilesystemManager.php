<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Filesystem;

use think\File;
use think\filesystem\driver\Local;
use Xin\Capsule\Manager;
use Xin\Filesystem\Factory as FilesystemFactory;
use Xin\Support\Arr;

/**
 * Class FilesystemManager
 *
 * @property-read \think\App app
 * @mixin \Xin\Filesystem\Filesystem
 */
class FilesystemManager extends Manager
{

	/**
	 * @param null|string $name
	 * @return \Xin\Filesystem\Filesystem
	 */
	public function disk($name = null)
	{
		return $this->driver($name);
	}

	/**
	 * 获取缓存配置
	 *
	 * @access public
	 * @param null|string $name 名称
	 * @param mixed $default 默认值
	 * @return mixed
	 */
	public function getConfig($name = null, $default = null)
	{
		if (!is_null($name)) {
			return $this->app->config->get('filesystem.' . $name, $default);
		}

		return $this->app->config->get('filesystem');
	}

	/**
	 * 获取磁盘配置
	 *
	 * @param string $disk
	 * @param null $name
	 * @param null $default
	 * @return mixed
	 */
	public function getDiskConfig($disk, $name = null, $default = null)
	{
		if ($config = $this->getConfig("disks.{$disk}")) {
			return Arr::get($config, $name, $default);
		}

		throw new \InvalidArgumentException("Disk [$disk] not found.");
	}

	/**
	 * 解析类型
	 * @param string $name
	 * @return string
	 */
	protected function resolveType($name)
	{
		return $this->getDiskConfig($name, 'type', 'local');
	}

	/**
	 * 解析配置
	 * @param string $name
	 * @return array
	 */
	protected function resolveConfig($name)
	{
		return $this->getDiskConfig($name);
	}

	/**
	 * 默认驱动
	 *
	 * @return string|null
	 */
	public function getDefaultDriver()
	{
		return $this->getConfig('default');
	}

	/**
	 * 保存文件
	 *
	 * @param string $path 路径
	 * @param File $file 文件
	 * @param null|string|\Closure $rule 文件名规则
	 * @param array $options 参数
	 * @return bool|string
	 */
	public function putFile(string $path, File $file, $rule = null, array $options = [])
	{
		return $this->putFileAs($path, $file, $file->hashName($rule), $options);
	}

	/**
	 * 指定文件名保存文件
	 *
	 * @param string $path 路径
	 * @param File $file 文件
	 * @param string $name 文件名
	 * @param array $options 参数
	 * @return bool|string
	 */
	public function putFileAs(string $path, File $file, string $name, array $options = [])
	{
		$stream = fopen($file->getRealPath(), 'rb');
		$path = trim($path . '/' . $name, '/');

		$result = $this->putStream($path, $stream, $options);

		if (is_resource($stream)) {
			fclose($stream);
		}

		return $result ? $path : false;
	}

	/**
	 * 获取公开路径
	 *
	 * @param string $savePath
	 * @param string $disk
	 * @return string
	 */
	public function publicPath($savePath, $disk = null)
	{
		$disk = $disk ?: $this->getDefaultDriver();
		$domain = $this->getDiskConfig($disk, 'url');

		return $domain . '/' . str_replace("\\", "/", $savePath);
	}

	/**
	 * 本地驱动器
	 *
	 * @param array $config
	 * @return mixed
	 */
	protected function createLocalDriver(array $config)
	{
		return $this->app->make(Local::class, [$config]);
	}

	/**
	 * 七牛驱动器
	 *
	 * @param array $config
	 * @return FilesystemProxy
	 * @throws \Xin\Filesystem\FilesystemException
	 */
	protected function createQiniuDriver(array $config)
	{
		if (!class_exists('Qiniu\Auth')) {
			throw new \LogicException("请先安装七牛云驱动！");
		}

		return new FilesystemProxy(FilesystemFactory::qiniu($config));
	}

	/**
	 * 阿里云OSS驱动器
	 *
	 * @param array $config
	 * @return FilesystemProxy
	 * @throws \Xin\Filesystem\FilesystemException
	 */
	protected function createAliyunDriver(array $config)
	{
		if (!class_exists('OSS\OssClient')) {
			throw new \LogicException("请先安装阿里云OSS驱动！");
		}

		return new FilesystemProxy(FilesystemFactory::aliyun($config));
	}

	/**
	 * 腾讯云COS驱动器
	 *
	 * @param array $config
	 * @return FilesystemProxy
	 * @throws \Xin\Filesystem\FilesystemException
	 */
	protected function createQCloudDriver(array $config)
	{
		if (!class_exists('Qcloud\Cos\Client')) {
			throw new \LogicException("请先安装腾讯云COS驱动！");
		}

		return new FilesystemProxy(FilesystemFactory::qcloud($config));
	}

}
