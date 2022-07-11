<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Payment;

use Xin\Contracts\Payment\Factory as PaymentFactory;
use Xin\Payment\Exceptions\PaymentNotConfigureException;
use Xin\Support\Arr;
use Xin\Support\File;
use Yansongda\Pay\Pay;

class PaymentManager implements PaymentFactory
{

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * Payment constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function wechat($name = null, array $options = [])
	{
		$name = $name ?: $this->getDefault('wechat');

		$config = $this->getConfig("wechat.{$name}");
		if (empty($config)) {
			throw new PaymentNotConfigureException("payment config 'wechat.{$name}' not defined.");
		}

		return $this->factoryWechat($config, $options);
	}

	/**
	 * 构建微信支付实例
	 * @param array $config
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Wechat
	 */
	protected function factoryWechat($config, $options)
	{
		$config = $this->initWechatConfig($config, $options);

		$config = array_merge($this->getConfig('defaults'), $config);

		return $this->initApplication(
			Pay::wechat($config),
			$options
		);
	}


	/**
	 * 初始化微信配置信息
	 *
	 * @param array $config
	 * @return array
	 */
	protected function initWechatConfig($config, $options)
	{
		if (isset($config['appid'])) {
			// fix official
			if (!isset($config['app_id'])) {
				$config['app_id'] = $config['appid'];
			}

			// fix miniapp
			if (!isset($config['miniapp_id'])) {
				$config['miniapp_id'] = $config['appid'];
			}
		}

		// cert support
		if (isset($options['cert'])) {
			if (isset($config['cert_client_content'])) {
				$config['cert_client'] = File::putTempFile($config['cert_client_content']);
			}

			if (isset($config['cert_key_content'])) {
				$config['cert_key'] = File::putTempFile($config['cert_key_content']);
			}
		}

		return $config;
	}

	/**
	 * @inheritDoc
	 */
	public function hasWechat($name = null)
	{
		$name = $name ?: $this->getDefault('wechat');
		$key = 'wechat.' . $name;
		return $this->hasConfig($key) &&
			$this->hasConfig($key . '.mch_id') &&
			$this->hasConfig($key . '.key');
	}

	/**
	 * @inheritDoc
	 */
	public function alipay($name = null, array $options = [])
	{
		$name = $name ?: $this->getDefault('alipay');

		$config = $this->getConfig("alipay.{$name}");
		if (empty($config)) {
			throw new PaymentNotConfigureException("payment config 'alipay.{$name}' not defined.");
		}

		return $this->factoryAlipay($config, $options);
	}

	/**
	 * 构建支付宝实例
	 * @param array $config
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Alipay
	 */
	protected function factoryAlipay(array $config, array $options)
	{
		$config = $this->initAlipayConfig($config, $options);

		$config = array_merge($this->getConfig('defaults'), $config);

		return $this->initApplication(
			Pay::alipay($config),
			$options
		);
	}

	/**
	 * 初始化支付宝配置信息
	 * @param array $config
	 * @param array $options
	 * @return array
	 */
	protected function initAlipayConfig(array $config, array $options)
	{
		return $config;
	}

	/**
	 * @inheritDoc
	 */
	public function hasAlipay($name = null)
	{
		$name = $name ?: $this->getDefault('alipay');
		return $this->hasConfig('alipay.' . $name);
	}

	/**
	 * @inheritDoc
	 */
	public function getConfig($key = null, $default = null)
	{
		if (null === $key) {
			return $this->config;
		}

		return Arr::get($this->config, $key, $default);
	}


	/**
	 * 指定类型的配置是否存在
	 *
	 * @param string $key
	 * @return bool
	 */
	public function hasConfig($key = null)
	{
		if ($key == null) {
			return !empty($this->config);
		}

		return Arr::has($this->config, $key);
	}

	/**
	 * 初始化
	 *
	 * @param mixed $driver
	 * @param array $options
	 * @return mixed
	 */
	protected function initApplication($driver, array $options = [])
	{
		return $driver;
	}

	/**
	 * @return string
	 */
	protected function getDefault($type)
	{
		return Arr::get($this->config, "defaults.{$type}", 'default');
	}

}
