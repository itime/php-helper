<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Saas\Wechat;

use Xin\Contracts\Saas\Wechat\WechatConfigProvider;
use Xin\Contracts\Saas\Wechat\WechatRepository;
use Xin\Wechat\WechatManager as BaseWechatManager;

class WechatManager extends BaseWechatManager implements WechatRepository {

	/**
	 * @var WechatConfigProvider
	 */
	protected $configProvider;

	/**
	 * @inheritDoc
	 */
	public function __construct(array $config, WechatConfigProvider $configProvider) {
		parent::__construct($config);

		$this->configProvider = $configProvider;
	}

}
