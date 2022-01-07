<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Model;

class AppContext {

	/**
	 * @var int
	 */
	protected $globalAppId = null;

	/**
	 * @var bool
	 */
	protected $enableGlobalAppId = false;

	/**
	 * @var callable
	 */
	protected $globalAppIdResolver = null;

	/**
	 * @var static
	 */
	protected static $instance = null;

	/**
	 * MultiAppContext constructor.
	 */
	protected function __construct() {
	}

	/**
	 * @return int
	 */
	public function getGlobalAppId() {
		if ($this->globalAppId === null && $this->globalAppIdResolver) {
			$this->globalAppId = call_user_func($this->globalAppIdResolver);
		}

		return $this->globalAppId;
	}

	/**
	 * @param int $appId
	 */
	public function setGlobalAppId($appId) {
		$this->globalAppId = (int)$appId;
	}

	/**
	 * @param bool $enable
	 */
	public function enableGlobalAppId($enable = true) {
		$this->enableGlobalAppId = $enable;
	}

	/**
	 * @return bool
	 */
	public function isEnableGlobalAppId() {
		return $this->enableGlobalAppId;
	}

	/**
	 * @param callable $resolver
	 */
	public function setGlobalAppIdResolver(callable $resolver) {
		$this->globalAppIdResolver = $resolver;
	}

	/**
	 * @return static
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new static();
		}

		return self::$instance;
	}

}
