<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\Response;

class ApiHandler extends AbstractHandler {

	/**
	 * @inheritDoc
	 */
	public function render($data = null) {
		return Response::create($this->optimizeData($data), 'json');
	}

	/**
	 * @inheritDoc
	 */
	public function isAjax() {
		return true;
	}

}
