<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Filesystem;

use League\Flysystem\Filesystem;
use think\File;

/**
 * Class FilesystemAdapter
 *
 * @mixin \Xin\Filesystem\Filesystem
 */
class FilesystemAdapter
{

	/**
	 * @var Filesystem
	 */
	protected $target;

	/**
	 * FilesystemProxy constructor.
	 *
	 * @param mixed $target
	 */
	public function __construct($target)
	{
		$this->target = $target;
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
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->target, $name], $arguments);
	}

	/**
	 * @return Filesystem
	 */
	public function getFilesystem()
	{
		return $this->target;
	}

}
