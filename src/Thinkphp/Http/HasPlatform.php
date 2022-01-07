<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Http;

/**
 * @mixin Requestable
 */
trait HasPlatform {

	/**
	 * @var string
	 */
	protected $platform;

	/**
	 * 获取当前请求平台
	 *
	 * @return string
	 */
	public function platform() {
		if (is_null($this->platform)) {
			$userAgent = $this->server('HTTP_USER_AGENT');
			$referer = $this->server('HTTP_REFERER');

			if (stripos($userAgent, "wechatdevtools") !== false
				|| stripos($userAgent, "MicroMessenger") !== false
				|| stripos($referer, "servicewechat.com") !== false) {
				$this->platform = 'wechat';
			} elseif (stripos($userAgent, "AliApp") !== false
				|| stripos($userAgent, "Alipay") !== false
				|| stripos($userAgent, "AlipayDefined") !== false
				|| stripos($userAgent, "AlipayClient") !== false
				|| stripos($userAgent, "支付宝") !== false
				|| stripos(urldecode($userAgent), "支付宝") !== false) {
				$this->platform = 'alipay';
			} elseif (stripos($userAgent, "swandevtools") !== false
				|| stripos($userAgent, "baiduboxapp") !== false
				|| stripos($userAgent, "baiduboxapp") !== false
				|| stripos($referer, "smartapp.baidu.com") !== false) {
				$this->platform = 'baidu';
			} else {
				$this->platform = 'browser';
			}
		}

		return $this->platform;
	}

	/**
	 * 是否是微信请求
	 *
	 * @return bool
	 */
	public function isFromWechat() {
		return "wechat" === $this->platform();
	}

	/**
	 * 是否是支付宝请求
	 *
	 * @return bool
	 */
	public function isFromAlipay() {
		return "alipay" === $this->platform();
	}

	/**
	 * 是否是百度请求
	 *
	 * @return bool
	 */
	public function isFromBaidu() {
		return "baidu" === $this->platform();
	}

	/**
	 * 是否是浏览器请求
	 *
	 * @return bool
	 */
	public function isFromBrowser() {
		return "browser" === $this->platform();
	}

}
