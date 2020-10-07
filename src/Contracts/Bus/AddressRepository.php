<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Bus;

use Xin\Contracts\Foundation\Repository as BaseRepository;

interface AddressRepository extends BaseRepository{
	
	/**
	 * 获取用户的收获地址列表
	 *
	 * @param int $userId
	 * @return mixed
	 */
	public function getByUser($userId);
	
	/**
	 * 获取用户的默认收获地址
	 *
	 * @param int $userId
	 * @return mixed
	 */
	public function getDefaultByUser($userId);
	
	/**
	 * 设置用户的默认收获地址
	 *
	 * @param int $userId
	 * @return mixed
	 */
	public function setDefaultByUser($userId);
	
	/**
	 * 更新用户的收获地址信息
	 *
	 * @param int   $id
	 * @param int   $userId
	 * @param array $attributes
	 * @return mixed
	 */
	public function updateByUser($id, $userId, array $attributes);
}
