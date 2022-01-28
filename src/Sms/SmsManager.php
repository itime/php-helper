<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Sms;

use Overtrue\EasySms\EasySms;
use Xin\Capsule\Manager;
use Xin\Contracts\Sms\Factory;
use Xin\Support\Arr;

/**
 * @mixin \Xin\Contracts\Sms\Channel
 */
class SmsManager extends Manager implements Factory
{

	/**
	 * @var \Overtrue\EasySms\EasySms
	 */
	protected $service;

	/**
	 * @inerhitDoc
	 */
	public function channel($name = null)
	{
		return $this->driver($name);
	}

	/**
	 * 创建EasySms实例
	 * @param string $name
	 * @param array $config
	 * @return HigherOrderEasySmsProxy
	 * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
	 */
	public function createDefaultDriver($name, array $config)
	{
		return new HigherOrderEasySmsProxy(
			$this->service()->gateway($name), $config
		);
	}

	/**
	 * @inerhitDoc
	 */
	protected function getDefaultDriver()
	{
		return $this->getConfig('defaults.channel', 'default');
	}

	/**
	 * @inerhitDoc
	 */
	protected function setDefaultDriver($name)
	{
		return $this->getConfig('defaults.channel', 'default');
	}

	/**
	 * @inerhitDoc
	 */
	public function getDriverConfig($name)
	{
		$key = 'channels';

		return $this->getConfig($name ? "{$key}.{$name}" : $key);
	}

	/**
	 * @return EasySms
	 */
	protected function service()
	{
		if (!$this->service) {
			$this->service = new EasySms(
				Arr::transformKeys($this->config, [
					'defaults' => 'default',
					'channels' => 'gateways',
				])
			);
		}

		return $this->service;
	}

}
