<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Wechat;

use think\Service;
use Xin\Contracts\Foundation\Wechat as BaseWechatContract;
use Xin\Contracts\Saas\WechatRepository;
use Xin\Foundation\Wechat\Wechat as BaseWechat;

class WechatServiceProvider extends Service {

	/**
	 * 启动器
	 */
	public function register() {
		$this->app->bind(BaseWechat::class, WechatRepository::class);
		$this->app->bind(BaseWechatContract::class, WechatRepository::class);
		$this->app->bind(WechatRepository::class, function () {
			return new Wechat(
				$this->app->config->get('wechat')
			);
		});
	}

}
