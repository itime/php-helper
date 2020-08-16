<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Setting;

use think\facade\Config;
use Xin\Thinkphp\provider\ServiceProvider;

class SettingServiceProvider extends ServiceProvider{

	/**
	 * @inheritDoc
	 */
	public function register(){
		//注入配置信息
		$settings = Setting::load();
		Config::set([
			'web' => $settings,
		]);
	}

}
