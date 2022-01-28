<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Saas\Wechat;

use Xin\Contracts\Saas\Wechat\ConfigProvider;
use Xin\Contracts\Saas\Wechat\Repository;
use Xin\Contracts\Saas\Wechat\WechatType;
use Xin\Wechat\Exceptions\WechatNotConfigureException;
use Xin\Wechat\WechatManager as BaseWechatManager;

class WechatManager extends BaseWechatManager implements Repository
{

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
	protected $lockMiniProgramId;

	/**
	 * @var int
	 */
	protected $lockOfficialAccountId;

	/**
	 * @var int
	 */
	protected $lockOpenPlatformId;

	/**
	 * @var int
	 */
	protected $lockWorkId;

	/**
	 * @var int
	 */
	protected $lockOpenWorkId;

	/**
	 * @inheritDoc
	 */
	public function __construct(array $config, ConfigProvider $configProvider)
	{
		parent::__construct($config);

		$this->configProvider = $configProvider;
	}

	/**
	 * @inheritDoc
	 */
	public function openPlatform($name = null, array $options = [])
	{
		$default = $options['default'] ?? false;

		if (!$default) {
			if ($this->lockOpenPlatformId) {
				return $this->openPlatformOfId($this->lockOpenPlatformId, $options);
			}

			if ($this->lockAppId) {
				return $this->openPlatformOfAppId($this->lockAppId, $options);
			}
		}

		return parent::openPlatform($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function openPlatformOfId($id, array $options = [])
	{
		$config = $this->configProvider->retrieveById($id, WechatType::OPEN_PLATFORM);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat open_platform config of id {$id} not defined.");
		}

		return $this->factoryOpenPlatform($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function openPlatformOfAppId($appId, $name = null, array $options = [])
	{
		$config = $this->configProvider->retrieveByAppId($appId, WechatType::OFFICIAL_ACCOUNT, $name);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat open_platform config of app_id {$appId} with name '{$name}' not defined.");
		}

		return $this->factoryOpenPlatform($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function officialAccount($name = null, array $options = [])
	{
		$default = $options['default'] ?? false;

		if (!$default) {
			if ($this->lockOfficialAccountId) {
				return $this->officialAccountOfId($this->lockOfficialAccountId, $options);
			}

			if ($this->lockAppId) {
				return $this->officialAccountOfAppId($this->lockAppId, $options);
			}
		}

		return parent::officialAccount($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function officialAccountOfId($id, array $options = [])
	{
		$config = $this->configProvider->retrieveById($id, WechatType::OFFICIAL_ACCOUNT);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat official_account config of id {$id} not defined.");
		}

		return $this->factoryOfficialAccount($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function officialAccountOfAppId($appId, $name = null, array $options = [])
	{
		$config = $this->configProvider->retrieveByAppId($appId, WechatType::OFFICIAL_ACCOUNT, $name);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat official_account config of app_id {$appId} with name '{$name}' not defined.");
		}

		return $this->factoryOfficialAccount($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function miniProgram($name = null, array $options = [])
	{
		$default = $options['default'] ?? false;

		if (!$default) {
			if ($this->lockMiniProgramId) {
				return $this->miniProgramOfId($this->lockMiniProgramId, $options);
			}

			if ($this->lockAppId) {
				return $this->miniProgramOfAppId($this->lockAppId, $options);
			}
		}

		return parent::miniProgram($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function miniProgramOfId($id, array $options = [])
	{
		$config = $this->configProvider->retrieveById($id, WechatType::MINI_PROGRAM);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat mini_program config of id {$id} not defined.");
		}

		return $this->factoryMiniProgram($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function miniProgramOfAppId($appId, $name = null, array $options = [])
	{
		$config = $this->configProvider->retrieveByAppId($appId, WechatType::MINI_PROGRAM, $name);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat mini_program config of app_id {$appId} with name '{$name}' not defined.");
		}

		return $this->factoryMiniProgram($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function work($name = null, array $options = [])
	{
		$default = $options['default'] ?? false;

		if (!$default) {
			if ($this->lockWorkId) {
				$options['name'] = $name;

				return $this->workOfId($this->lockMiniProgramId, $options);
			}

			if ($this->lockAppId) {
				return $this->workOfAppId($this->lockAppId, $name, $options);
			}
		}

		return parent::work($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function workOfId($id, array $options = [])
	{
		$config = $this->configProvider->retrieveById($id, WechatType::WORK);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat work config of id {$id} not defined.");
		}

		return $this->factoryWork($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function workOfAppId($appId, $name = null, array $options = [])
	{
		$config = $this->configProvider->retrieveByAppId($appId, WechatType::WORK, $name);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat work config of app_id {$appId} with name '{$name}' not defined.");
		}

		return $this->factoryWork($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function openWork($name = null, array $options = [])
	{
		$default = $options['default'] ?? false;

		if (!$default) {
			if ($this->lockMiniProgramId) {
				$options['name'] = $name;

				return $this->openWorkOfId($this->lockMiniProgramId, $options);
			}

			if ($this->lockAppId) {
				return $this->workOfAppId($this->lockAppId, $name, $options);
			}
		}

		return parent::openWork($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function openWorkOfId($id, array $options = [])
	{
		$config = $this->configProvider->retrieveById($id, WechatType::OPEN_WORK);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat open_work config of id {$id} not defined.");
		}

		return $this->factoryOpenWork($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function openWorkOfAppId($appId, $name = null, array $options = [])
	{
		$config = $this->configProvider->retrieveByAppId($appId, WechatType::OPEN_WORK, $name);

		if (empty($config)) {
			throw new WechatNotConfigureException("wechat open_work config of app_id {$appId} with name '{$name}' not defined.");
		}

		return $this->factoryOpenWork($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseOfAppId($appId)
	{
		$this->lockAppId = $appId;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseOfOpenPlatformId($id)
	{
		$this->lockOpenPlatformId = $id;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseOfOfficialAccountOfId($id)
	{
		$this->lockOfficialAccountId = $id;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseMiniProgramOfId($id)
	{
		$this->lockMiniProgramId = $id;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseWorkId($id)
	{
		$this->lockWorkId = $id;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldUseOpenWorkId($id)
	{
		$this->lockOpenWorkId = $id;
	}

}
