<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Foundation\Payment;

use Xin\Contracts\Foundation\Payment as PaymentContract;
use Yansongda\Pay\Pay;

class Payment implements PaymentContract{

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * Payment constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config){
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function wechat(array $options = []){
		if(!isset($this->config['wechat']) || empty($this->config['wechat'])){
			throw new PaymentNotConfigureException("payment config 'wechat' not defined.");
		}

		$config = $this->initWechatConfig($this->config['wechat']);

		return $this->initApplication(
			Pay::wechat($config),
			$options
		);
	}

	/**
	 * @inheritDoc
	 */
	public function alipay(array $options = []){
		if(!isset($this->config['alipay']) || empty($this->config['alipay'])){
			throw new PaymentNotConfigureException("payment config 'alipay' not defined.");
		}

		$config = $this->config['alipay'];

		return $this->initApplication(
			Pay::alipay($config),
			$options
		);
	}

	/**
	 * 初始化
	 *
	 * @param mixed $driver
	 * @param array $options
	 * @return mixed
	 */
	protected function initApplication($driver, array $options = []){
		return $driver;
	}

	/**
	 * 初始化微信配置信息
	 *
	 * @param array $config
	 * @return array
	 */
	protected function initWechatConfig($config){
		if(isset($config['appid'])){
			// fix official
			if(!isset($config['app_id'])){
				$config['app_id'] = $config['appid'];
			}

			// fix miniapp
			if(!isset($config['miniapp_id'])){
				$config['miniapp_id'] = $config['appid'];
			}
		}

		return $config;
	}
}
