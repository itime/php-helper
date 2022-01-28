<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Sms;

use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Message;
use Overtrue\EasySms\Messenger;
use Overtrue\EasySms\PhoneNumber;
use Overtrue\EasySms\Support\Config;
use Xin\Capsule\Service;
use Xin\Contracts\Sms\Channel;

class HigherOrderEasySmsProxy extends Service implements Channel
{

	/**
	 * @var GatewayInterface
	 */
	protected $gateway;

	/**
	 * @param GatewayInterface $gateway
	 * @param array $config
	 */
	public function __construct(GatewayInterface $gateway, array $config)
	{
		parent::__construct($config);
		$this->gateway = $gateway;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function send($phone, $message)
	{
		$phone = $this->formatPhoneNumber($phone);
		$message = $this->formatMessage($message);

		try {
			$result = [
				'success' => true,
				'fail' => false,
				'gateway' => $this->gateway->getName(),
				'status' => Messenger::STATUS_SUCCESS,
				'result' => $this->gateway->send($phone, $message, new Config($this->config)),
			];
		} catch (\Exception $e) {
			$result = [
				'success' => false,
				'fail' => true,
				'gateway' => $this->gateway->getName(),
				'status' => Messenger::STATUS_FAILURE,
				'exception' => $e,
			];
		} catch (\Throwable $e) {
			$result = [
				'success' => false,
				'fail' => true,
				'gateway' => $this->gateway->getName(),
				'status' => Messenger::STATUS_FAILURE,
				'exception' => $e,
			];
		}

		return $result;
	}

	/**
	 * @param string|\Overtrue\EasySms\Contracts\PhoneNumberInterface $number
	 *
	 * @return \Overtrue\EasySms\Contracts\PhoneNumberInterface
	 */
	protected function formatPhoneNumber($number)
	{
		if ($number instanceof PhoneNumberInterface) {
			return $number;
		}

		return new PhoneNumber(\trim($number));
	}

	/**
	 * @param array|string|\Overtrue\EasySms\Contracts\MessageInterface $message
	 *
	 * @return \Overtrue\EasySms\Contracts\MessageInterface
	 */
	protected function formatMessage($message)
	{
		if (!($message instanceof MessageInterface)) {
			if (!\is_array($message)) {
				$message = [
					'content' => $message,
					'template' => $message,
				];
			}

			$message = new Message($message);
		}

		return $message;
	}


}
