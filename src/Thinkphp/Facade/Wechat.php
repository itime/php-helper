<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Facade;

use think\Facade;

/**
 * @method static \EasyWeChat\OpenPlatform\Application defaultOpenPlatform($options = [])
 * @method static \EasyWeChat\OfficialAccount\Application defaultOfficial($options = [])
 * @method static \EasyWeChat\MiniProgram\Application defaultMiniProgram($options = [])
 * @mixin \Xin\Thinkphp\Foundation\Wechat
 */
class Wechat extends Facade{
	
	/**
	 * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
	 *
	 * @access protected
	 * @return string
	 */
	protected static function getFacadeClass(){
		return 'wechat';
	}
	
}
