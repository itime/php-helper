<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Uploader;

interface UploadProvider
{
	/**
	 * 根据ID获取文件信息
	 * @param string $scene
	 * @param int $id
	 * @return array
	 */
	public function retrieveById($scene, $id);

	/**
	 * 根据文件hash值算法获取文件信息
	 * @param string $scene
	 * @param string $hashType
	 * @param string $hash
	 * @return array
	 */
	public function retrieveByHash($scene, $hashType, $hash);

	/**
	 * @param string $scene
	 * @param array $data
	 * @return array
	 */
	public function save($scene, array $data);
}