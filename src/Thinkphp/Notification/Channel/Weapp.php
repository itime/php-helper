<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Notification\Channel;

use think\App;
use think\facade\Log;
use Xin\Support\WechatResult;
use Xin\Thinkphp\Notification\Message\WxSubscribeMessage;
use yunwuxin\Notification;
use yunwuxin\notification\Channel;

class Weapp extends Channel {

	/**
	 * @var \think\App
	 */
	protected $app;

	public function __construct(App $app) {
		$this->app = $app;
	}

	/**
	 * @inheritDoc
	 */
	public function send($notifiable, Notification $notification) {
		$message = $this->getMessage($notifiable, $notification);

		if (!$message instanceof WxSubscribeMessage) {
			return;
		}

		$result = $this->miniprogram($notification)->subscribe_message->send($message->toArray());
		WechatResult::make($result)->then(null, function ($result) {
			Log::info('发送通知失败：' . json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		});
	}

	/**
	 * 获取小程序实例
	 *
	 * @return \EasyWeChat\MiniProgram\Application
	 */
	protected function miniprogram(Notification $notification) {
		/** @var \Xin\Contracts\Foundation\Wechat $ws */
		$ws = $this->app['wechat'];

		return $ws->miniProgram();
	}

}
