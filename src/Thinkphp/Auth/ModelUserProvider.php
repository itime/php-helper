<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use Xin\Contracts\Auth\UserProvider as UserProviderContract;

class ModelUserProvider implements UserProviderContract
{

	use UserProviderHelpers;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * Create a new model user provider.
	 *
	 * @param array $config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * @return \think\Db|\think\db\Query
	 */
	protected function query()
	{
		$model = $this->config['model'];

		return new $model();
	}

}
