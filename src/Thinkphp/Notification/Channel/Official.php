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
use Xin\Thinkphp\Notification\Message\WxTemplateMessage;
use yunwuxin\Notification;
use yunwuxin\notification\Channel;

class Official extends Channel
{

	/**
	 * @var \think\App
	 */
	protected $app;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * @inheritDoc
	 */
	public function send($notifiable, Notification $notification)
	{
		$message = $this->getMessage($notifiable, $notification);

		if (!$message instanceof WxTemplateMessage) {
			return;
		}

		$result = $this->official($notification)->template_message->send($message->toArray());
		WechatResult::make($result)->then(null, function ($result) {
			Log::info('发送通知失败：' . json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		});
	}

	/**
	 * 获取公众号实例
	 *
	 * @return \EasyWeChat\OfficialAccount\Application
	 */
	protected function official(Notification $notification)
	{
		/** @var \Xin\Contracts\Foundation\Wechat $ws */
		$ws = $this->app['wechat'];

		return $ws->official();
	}

}
