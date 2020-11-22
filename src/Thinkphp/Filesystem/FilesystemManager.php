<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Filesystem;

use think\File;
use think\filesystem\driver\Local;
use Xin\Filesystem\Adapter\Aliyun\Aliyun;
use Xin\Filesystem\Adapter\QCloud\QCloud;
use Xin\Filesystem\Adapter\Qiniu\Qiniu;
use Xin\Filesystem\Filesystem;
use Xin\Support\Arr;
use Xin\Support\Manager;

/**
 * Class FilesystemManager
 *
 * @property-read \think\App app
 */
class FilesystemManager extends Manager{
	
	/**
	 * @param null|string $name
	 * @return \Xin\Filesystem\Filesystem
	 */
	public function disk($name = null){
		return $this->driver($name);
	}
	
	/**
	 * 获取缓存配置
	 *
	 * @access public
	 * @param null|string $name 名称
	 * @param mixed       $default 默认值
	 * @return mixed
	 */
	public function getConfig($name = null, $default = null){
		if(!is_null($name)){
			return $this->app->config->get('filesystem.'.$name, $default);
		}
		
		return $this->app->config->get('filesystem');
	}
	
	/**
	 * 获取磁盘配置
	 *
	 * @param string $disk
	 * @param null   $name
	 * @param null   $default
	 * @return array
	 */
	public function getDiskConfig($disk, $name = null, $default = null){
		if($config = $this->getConfig("disks.{$disk}")){
			return Arr::get($config, $name, $default);
		}
		
		throw new \InvalidArgumentException("Disk [$disk] not found.");
	}
	
	/**
	 * @param string $name
	 * @return array|mixed|string
	 */
	protected function resolveType($name){
		return $this->getDiskConfig($name, 'type', 'local');
	}
	
	/**
	 * @param string $name
	 * @return array|mixed|string
	 */
	protected function resolveConfig($name){
		return $this->getDiskConfig($name);
	}
	
	/**
	 * 默认驱动
	 *
	 * @return string|null
	 */
	public function getDefaultDriver(){
		return $this->getConfig('default');
	}
	
	/**
	 * 保存文件
	 *
	 * @param string               $path 路径
	 * @param File                 $file 文件
	 * @param null|string|\Closure $rule 文件名规则
	 * @param array                $options 参数
	 * @return bool|string
	 */
	public function putFile(string $path, File $file, $rule = null, array $options = []){
		return $this->putFileAs($path, $file, $file->hashName($rule), $options);
	}
	
	/**
	 * 指定文件名保存文件
	 *
	 * @param string $path 路径
	 * @param File   $file 文件
	 * @param string $name 文件名
	 * @param array  $options 参数
	 * @return bool|string
	 */
	public function putFileAs(string $path, File $file, string $name, array $options = []){
		$stream = fopen($file->getRealPath(), 'r');
		$path = trim($path.'/'.$name, '/');
		
		$result = $this->putStream($path, $stream, $options);
		
		if(is_resource($stream)){
			fclose($stream);
		}
		
		return $result ? $path : false;
	}
	
	/**
	 * 本地驱动器
	 *
	 * @param array $config
	 * @return mixed
	 */
	protected function createLocalDriver(array $config){
		return $this->app->make(Local::class, [$config]);
	}
	
	/**
	 * 七牛驱动器
	 *
	 * @param array $config
	 * @return mixed
	 * @throws \Xin\Filesystem\FilesystemException
	 */
	protected function createQiniuDriver(array $config){
		return new FilesystemProxy(
			new Filesystem(new Qiniu($config))
		);
	}
	
	/**
	 * 阿里云OSS驱动器
	 *
	 * @param array $config
	 * @return mixed
	 * @throws \Xin\Filesystem\FilesystemException
	 */
	protected function createAliyunDriver(array $config){
		return new FilesystemProxy(
			new Filesystem(new Aliyun($config))
		);
	}
	
	/**
	 * 腾讯云COS驱动器
	 *
	 * @param array $config
	 * @return mixed
	 * @throws \Xin\Filesystem\FilesystemException
	 */
	protected function createQCloudDriver(array $config){
		return new FilesystemProxy(
			new Filesystem(new QCloud($config))
		);
	}
}
