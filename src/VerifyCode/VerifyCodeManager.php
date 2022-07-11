<?php

namespace Xin\VerifyCode;

use Xin\Capsule\Manager;
use Xin\Contracts\VerifyCode\Factory;
use Xin\Contracts\VerifyCode\Sender;
use Xin\Contracts\VerifyCode\Store;

/**
 * @mixin \Xin\Contracts\VerifyCode\Repository
 */
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
		return $this->createRepository($config, new SmsSender($config));
	}

	/**
	 * 创建仓库实例
	 * @param array $config
	 * @param Sender $sender
	 * @return Repository
	 */
	protected function createRepository($config, $sender)
	{
		if (method_exists($sender, 'setContainer')) {
			$sender->setContainer($this->getContainer());
		}

		return new Repository(
			$this->createStore($config),
			$sender
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