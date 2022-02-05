<?php

namespace Xin\VerifyCode;

use Xin\Capsule\Manager;
use Xin\Contracts\VerifyCode\Factory;
use Xin\Contracts\VerifyCode\Store;

class VerifyCodeManager extends Manager implements Factory
{
	/**
	 * @var callable
	 */
	protected $storeResolver;

	/**
	 * @inerhitDoc
	 */
	public function sms()
	{
		return $this->use('sms');
	}

	/**
	 * @inerhitDoc
	 */
	public function email()
	{
		return $this->use('email');
	}

	/**
	 * @inerhitDoc
	 */
	public function use($name = null)
	{
		return $this->driver($name);
	}

	/**
	 * 创建短信验证码驱动
	 * @param string $name
	 * @param array $config
	 * @return Repository
	 */
	public function createSmsDriver($name, $config)
	{
		return new Repository(
			$this->createStore($config),
			new SmsSender($config)
		);
	}

	/**
	 * @return callable
	 */
	public function getStoreResolver(): callable
	{
		return $this->storeResolver;
	}

	/**
	 * @param callable $storeResolver
	 */
	public function setStoreResolver(callable $storeResolver): void
	{
		$this->storeResolver = $storeResolver;
	}

	/**
	 * 创建存储器
	 * @param array $config
	 * @return Store
	 */
	protected function createStore($config)
	{
		if (!$this->storeResolver) {
			throw new \RuntimeException("store resolver not defined.");
		}

		return call_user_func($this->storeResolver, $config);
	}

}