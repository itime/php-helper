<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Auth;

/**
 * @mixin \think\Model
 * @mixin \Xin\Contracts\Auth\UserWechatBinder
 */
trait UserWechatBinder{
	
	/**
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function getByOpenId($openid){
		return $this->baseQueryOfWechatBinder()
			->where($this->getOpenidFieldName(), $openid)
			->find();
	}
	
	/**
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public function bindToUser($userId, $openId){
		$info = $this->baseQueryOfWechatBinder()
			->where('id', $userId)
			->find();
		if(empty($info)){
			return true;
		}
		
		return $info->save([
			$this->getOpenidFieldName() => $openId,
		]);
	}
	
	/**
	 * @return $this
	 */
	protected function baseQueryOfWechatBinder(){
		return $this;
	}
	
	/**
	 * @return string
	 */
	protected function getOpenidFieldName(){
		return "openid";
	}
}
