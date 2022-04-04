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
	 * 根据Etag算法获取文件信息
	 * @param string $scene
	 * @param string $etag
	 * @return array
	 */
	public function getByETag($scene, $etag);

	/**
	 * 根据Md5算法获取文件信息
	 * @param string $scene
	 * @param string $md5
	 * @return array
	 */
	public function getByMd5($scene, $md5);

	/**
	 * 根据Sha1算法获取文件信息
	 * @param string $scene
	 * @param string $sha1
	 * @return array
	 */
	public function getBySha1($scene, $sha1);

	/**
	 * @param string $scene
	 * @param array $data
	 * @return array
	 */
	public function save($scene, array $data);
}