<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Upload;

use think\file\UploadedFile;
use think\Request;

/**
 * Trait UploadFile
 *
 * @property string disk
 * @property string savePath
 * @property string saveRule
 * @property string uploadName
 * @property string uploadType
 * @method array buildSaveData(array $data, UploadedFile $file = null)
 * @method array renderData(string $type, array $data)
 * @deprecated
 */
trait UploadFile
{

	use UploadFinder, UploadLocal, UploadToken;

	/**
	 * 要上传的文件类型
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function uploadType(Request $request)
	{
		if (property_exists($this, 'uploadType')) {
			return $this->uploadType;
		}

		return $request->param('type', 'image', 'trim');
	}

	/**
	 * 使用的驱动器
	 *
	 * @return string
	 */
	protected function disk()
	{
		if (property_exists($this, 'disk')) {
			return $this->disk;
		}

		return config('filesystem.default');
	}

	/**
	 * 保存路径
	 *
	 * @param string $type
	 * @return string
	 */
	protected function savePath($type)
	{
		if (property_exists($this, 'savePath')) {
			return $this->savePath;
		}

		return $type;
	}

	/**
	 * 生成文件规则
	 *
	 * @return string
	 */
	protected function saveRule()
	{
		if (property_exists($this, 'saveRule')) {
			return $this->saveRule;
		}

		return "md5";
	}

	/**
	 * 获取上传文件名
	 *
	 * @return string
	 */
	protected function uploadName()
	{
		if (property_exists($this, 'uploadName')) {
			return $this->uploadName;
		}

		return "file";
	}

}
