<?php

namespace Xin\VerifyCode;

use Xin\Contracts\VerifyCode\Repository as RepositoryContract;
use Xin\Contracts\VerifyCode\Sender;
use Xin\Contracts\VerifyCode\Store;
use Xin\Support\Str;

class Repository implements RepositoryContract
{
	/**
	 * @var Store
	 */
	protected $store;

	/**
	 * @var Sender
	 */
	protected $sender;

	/**
	 * @param Store $store
	 * @param Sender $sender
	 */
	public function __construct(Store $store, Sender $sender)
	{
		$this->store = $store;
		$this->sender = $sender;
	}

	/**
	 * @inerhitDoc
	 */
	public function make($identifier, $type = null, $options = [])
	{
		$options = $this->resolveMakeOptions($options);

		$code = $this->generateCode($options['length']);

		$this->store->save($type, $identifier, $code);

		return $this->sender->send($identifier, $code);
	}

	/**
	 * 解析 Make Options
	 * @param array $options
	 * @return array
	 */
	protected function resolveMakeOptions($options = [])
	{
		return array_merge([
			'length' => 6,
			'ttl' => 300
		], $options);
	}

	/**
	 * 生产 code
	 * @param int $length
	 * @return string
	 */
	protected function generateCode($length)
	{
		return Str::random($length, 0);
	}

	/**
	 * @inerhitDoc
	 */
	public function verify($identifier, $code, $type = null)
	{
		$lastCode = $this->store->get($type, $identifier);

		return $lastCode === (string)$code;
	}
}