<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Saas\Payment;

use Xin\Contracts\Saas\Payment\PaymentType;
use Xin\Contracts\Saas\Payment\Repository;
use Xin\Contracts\Saas\Wechat\ConfigProvider;
use Xin\Payment\Exceptions\PaymentNotConfigureException;
use Xin\Payment\PaymentManager as BasePaymentManager;

class PaymentManager extends BasePaymentManager implements Repository {

	/**
	 * @var ConfigProvider
	 */
	protected $configProvider;

	/**
	 * @var int
	 */
	protected $lockAppId;

	/**
	 * @var int
	 */
	protected $lockWechatId;

	/**
	 * @var int
	 */
	protected $lockAlipayId;

	/**
	 * @inheritDoc
	 */
	public function __construct(array $config, ConfigProvider $configProvider) {
		parent::__construct($config);

		$this->configProvider = $configProvider;
	}

	/**
	 * @inheritDoc
	 */
	public function wechat($name = null, array $options = []) {
		$default = $options['default'] ?? false;

		if (!$default) {
			if ($this->lockWechatId) {
				return $this->wechatOfId($this->lockWechatId, $options);
			}

			if ($this->lockAppId) {
				return $this->wechatOfAppId($this->lockAppId, $options);
			}
		}

		return parent::wechat($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function wechatOfId($id, array $options) {
		$config = $this->configProvider->retrieveById($id, PaymentType::WECHAT);

		if (empty($config)) {
			throw new PaymentNotConfigureException("payment wechat config of id {$id} not defined.");
		}

		return $this->factoryWechat($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function wechatOfAppId($appId, $name = null, array $options = []) {
		$config = $this->configProvider->retrieveByAppId($appId, PaymentType::WECHAT, $name);

		if (empty($config)) {
			throw new PaymentNotConfigureException("payment wechat config of app_id {$appId} with name '{$name}' not defined.");
		}

		return $this->factoryWechat($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function alipay($name = null, array $options = []) {
		$default = $options['default'] ?? false;

		if (!$default) {
			if ($this->lockAlipayId) {
				return $this->alipayOfId($this->lockAlipayId, $options);
			}

			if ($this->lockAppId) {
				return $this->alipayOfAppId($this->lockAppId, $options);
			}
		}

		return parent::alipay($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function alipayOfId($id, array $options = []) {
		$config = $this->configProvider->retrieveById($id, PaymentType::ALIPAY);

		if (empty($config)) {
			throw new PaymentNotConfigureException("payment alipay config of id {$id} not defined.");
		}

		return $this->factoryWechat($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function alipayOfAppId($appId, $name = null, array $options = []) {
		$config = $this->configProvider->retrieveByAppId($appId, PaymentType::ALIPAY, $name);

		if (empty($config)) {
			throw new PaymentNotConfigureException("payment alipay config of app_id {$appId} with name '{$name}' not defined.");
		}

		return $this->factoryAlipay($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseOfAppId($appId) {
		$this->lockAppId = $appId;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseOfWechatId($id) {
		$this->lockWechatId = $id;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseOfAlipayId($id) {
		$this->lockAlipayId = $id;
	}

}
