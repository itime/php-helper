<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

/**
 * Class UserProvider
 *
 * @mixin \think\Model
 * @mixin \Xin\Contracts\Auth\UserProvider
 */
trait UserProvider{
	
	/**
	 * @inheritDoc
	 */
	public function getById($identifier){
		return $this->find($identifier);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getByCredentials(array $credentials){
		return $this->where($credentials)->find();
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateCredentials(array $credentials){
	}
	
	/**
	 * @inheritDoc
	 */
	abstract public function validatePassword($user, $password);
}
