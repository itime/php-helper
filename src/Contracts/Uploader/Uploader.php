<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Uploader;

interface Uploader
{
	/**
	 * 上传文件
	 * @param string $scene
	 * @param string $targetPath
	 * @param \SplFileInfo $file
	 * @param array $options
	 * @return array
	 */
	public function file($scene, $targetPath, \SplFileInfo $file, array $options = []);

	/**
	 * 获取上传令牌
	 * @param string $scene
	 * @param string $targetPath
	 * @param array $options
	 * @return array
	 */
	public function token($scene, $targetPath, array $options = []);

	/**
	 * 转换异步回调数据
	 * @param array $notifyData
	 * @return array
	 */
	public function transformNotifyData($notifyData);

}