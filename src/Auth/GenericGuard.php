<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth;

/**
 * Class GenericGuard
 */
class GenericGuard extends AbstractGuard {

	/**
	 * @var callable|array
	 */
	protected $userResolver = null;

	/**
	 * GenericUser constructor.
	 *
	 * @param callable $userResolver
	 */
	public function __construct($userResolver) {
		parent::__construct('generic', [], null);
		$this->userResolver = $userResolver;
	}

	/**
	 * @return mixed
	 */
	protected function resolveUser() {
		if (!$this->user) {
			$this->user = call_user_func($this->userResolver);
		}

		return $this->user;
	}

}
