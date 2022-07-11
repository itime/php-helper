<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\Response;

class WebHandler extends AbstractHandler
{

	/**
	 * @inheritDoc
	 */
	public function render($data)
	{
		if ($this->isSuccess($data)) {
			$template = $this->service->getConfig('dispatch_success_tmpl');
			$data['wait'] = 1;
		} else {
			$template = $this->service->getConfig('dispatch_error_tmpl');
			$data['wait'] = 3;
		}

		/** @var \think\response\View $response */
		$response = Response::create($template ?: '', 'view');
		$response->assign($data);

		return $response;
	}

	/**
	 * @inheritDoc
	 */
	public function isAjax()
	{
		return false;
	}

}
