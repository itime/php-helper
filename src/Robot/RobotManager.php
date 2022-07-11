<?php

namespace Xin\Robot;


use Xin\Capsule\Manager;
use Xin\Contracts\Robot\Factory;

/**
 * @mixin \Xin\Contracts\Robot\Robot
 */
class RobotManager extends Manager implements Factory
{

	/**
	 * @inheritDoc
	 */
	public function robot($name = null)
	{
		return $this->driver($name);
	}

	/**
	 * 创建企微机器人
	 * @param string $name
	 * @param array $config
	 * @return QyWork
	 */
	public function createQyworkDriver($name, array $config)
	{
		return new QyWork($config);
	}

	/**
	 * 创建钉钉机器人
	 * @param string $name
	 * @param array $config
	 * @return DingTalk
	 */
	public function createDingTalkDriver($name, array $config): DingTalk
	{
		return new DingTalk($config, $name);
	}

	/**
	 * @inerhitDoc
	 */
	public function getDefaultDriver()
	{
		return $this->getConfig('defaults.robot', 'default');
	}

	/**
	 * @inerhitDoc
	 */
	public function setDefaultDriver($name)
	{
		$this->setConfig('defaults.robot', $name);
	}

	/**
	 * @inerhitDoc
	 */
	public function getDriverConfig($name)
	{
		$key = 'robots';

		return $this->getConfig($name ? "{$key}.{$name}" : $key);
	}

}
