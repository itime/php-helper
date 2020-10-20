<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Auth\Access\Abilities;

class RouteCheck{
	
	/**
	 * @param \app\common\model\Admin $user
	 */
	public function handle($user){
		halt($user, $user->roles()->select(), $user->getlastsql());
	}
}
