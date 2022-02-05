<?php

namespace Xin\VerifyCode;

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
		$this->sms()->send($identifier, [
			'template_id' => $this->getConfig('template_id', ''),
			'data' => [
				'code' => $code
			]
		]);
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