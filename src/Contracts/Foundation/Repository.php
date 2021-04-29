<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

interface Repository{

	/**
	 * 根据唯一标识符获取数据
	 *
	 * @param mixed $identifier
	 * @return mixed
	 */
	public function retrieveById($identifier);

	/**
	 * 根据凭证获取数据
	 *
	 * @param array|callable $credentials
	 * @param array          $options
	 * @return mixed
	 */
	public function retrieveByCredentials($credentials, array $options = []);
}
