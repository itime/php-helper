<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

use Xin\Support\Hasher;

/**
 * Class UserProvider
 *
 * @mixin \think\Model
 * @mixin \Xin\Contracts\Auth\UserProvider
 */
trait UserProviderHelpers{
	
	/**
	 * @return \think\Db|\think\db\Query
	 */
	abstract protected function query();
	
	/**
	 * @inheritDoc
	 */
	public function getById($identifier){
		return $this->query()->find($identifier);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getByCredentials($credentials){
		return $this->query()->where($credentials)->find();
	}
	
	/**
	 * @inheritDoc
	 */
	public function validatePassword($user, $password){
		$passwordName = $this->getPasswordName();
		$userPassword = $user[$passwordName];
		return (new Hasher())->check($userPassword, $password);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPasswordName(){
		return "password";
	}
}
