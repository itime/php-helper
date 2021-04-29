<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\Config;
use think\Request;
use think\Response;
use Xin\Contracts\Hint\Hint as HintContract;
use Xin\Support\Reflect;

class WebHint implements HintContract{

	use HintHelper;

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * @var \think\Config
	 */
	protected $config;

	/**
	 * ApiHint constructor.
	 *
	 * @param \think\Request $request
	 * @param \think\Config  $config
	 */
	public function __construct(Request $request, Config $config){
		$this->request = $request;
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function result($data = [], array $extend = []){
		return $this->success('操作成功！', null, $data, $extend);
	}

	/**
	 * @inheritDoc
	 */
	public function success($msg = '', $url = null, $data = null, array $extend = []){
		$url = $this->resolveSuccessUrl($url);

		$result = array_merge([
			'code' => 1,
			'msg'  => $msg,
			'data' => $data,
			'url'  => $url,
			'wait' => 1,
		], $extend);

		return $this->resolve(true, $result);
	}

	/**
	 * @inheritDoc
	 */
	public function error($msg, $code = 0, $url = null, array $extend = []){
		$url = $this->resolveErrorUrl($url);

		$result = array_merge([
			'code' => 0,
			'msg'  => $msg,
			'data' => null,
			'url'  => $url,
			'wait' => 3,
		], $extend);

		return $this->resolve(false, $result);
	}

	/**
	 * make Response
	 *
	 * @param bool  $isSuccess
	 * @param array $result
	 * @return \think\response\View
	 */
	protected function resolve($isSuccess, array $result){
		$configPrefix = Reflect::methodVisible($this->config, 'pull') == Reflect::VISIBLE_PUBLIC
			? 'app' : 'hint.web';

		if($isSuccess){
			$data = $this->config->get($configPrefix.'.dispatch_success_tmpl');
		}else{
			$data = $this->config->get($configPrefix.'.dispatch_error_tmpl');
		}

		/** @var \think\response\View $response */
		$response = Response::create($data ?: '', 'view');
		$response->assign($result);

		return $response;
	}

}
