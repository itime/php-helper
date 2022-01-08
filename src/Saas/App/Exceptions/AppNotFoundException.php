<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Saas\App\Exceptions;

class AppNotFoundException extends \LogicException {

	/**
	 * @param int $id
	 * @return static
	 */
	public static function ofId($id) {
		return new static("App not found [id:{$id}]!");
	}

	/**
	 * @param string $accessId
	 * @return static
	 */
	public static function ofAccessId($accessId) {
		return new static("App not found [accessId:{$accessId}]!");
	}

}
