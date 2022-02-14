<?php

namespace Xin\VerifyCode;

use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Xin\Capsule\Service;
use Xin\Contracts\Sms\Factory as SmsFactory;
use Xin\Contracts\VerifyCode\Sender;

class SmsSender extends Service implements Sender
{

	/**
	 * @inerhitDoc
	 */
	public function send($identifier, $code)
	{
		$result = $this->sms()->send($identifier, [
			'template' => $this->getConfig('template', ''),
			'data' => [
				'code' => $code
			]
		]);

		if (isset($result['exception'])) {
			/** @var GatewayErrorException $e */
			$e = $result['exception'];
			$message = $e->getMessage();
			if (stripos($message, '触发分钟级流控') !== -1) {
				throw new \LogicException("发送频繁，请稍后重试！");
			}
		}

		return $result['success'];
	}

	/**
	 * @return \Xin\Contracts\Sms\Channel
	 */
	protected function sms()
	{
		$channel = $this->getConfig('channel');

		/** @var SmsFactory $smsFactory */
		$smsFactory = $this->getContainer()->get(SmsFactory::class);

		return $smsFactory->channel($channel);
	}
}