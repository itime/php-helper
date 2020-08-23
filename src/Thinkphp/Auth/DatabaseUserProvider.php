<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use think\Db;
use Xin\Contracts\Auth\UserProvider as UserProviderContract;

class DatabaseUserProvider implements UserProviderContract{
	
	use UserProviderHelpers;
	
	/**
	 * @var \think\Db
	 */
	protected $db;
	
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * Create a new database user provider.
	 *
	 * @param \think\Db $db
	 * @param array     $config
	 */
	public function __construct(Db $db, $config){
		$this->db = $db;
		$this->config = $config;
	}
	
	/**
	 * @return \think\Db|\think\db\Query
	 */
	protected function query(){
		return $this->db->name($this->config['table']);
	}

}
