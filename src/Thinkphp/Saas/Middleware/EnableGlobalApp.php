<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Middleware;

use think\Model;
use Xin\Thinkphp\Foundation\Model\AppContext;

class EnableGlobalApp{

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * 多应用初始化
	 *
	 * @param \think\Request $request
	 * @param \Closure       $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next){
		$this->request = $request;

		$this->registerModelMaker();

		$context = AppContext::getInstance();
		$context->setGlobalAppIdResolver([$this, 'getGlobalAppId']);
		$context->enableGlobalAppId();

		return $next($request);
	}

	/**
	 * 注入模型 maker
	 */
	protected function registerModelMaker(){
		Model::maker(function(/**@var Model $model */ $model) use (&$initialized){
			$context = AppContext::getInstance();

			// 是否启用App全局作用域
			if($context->isEnableGlobalAppId()
				&& method_exists($model, 'withGlobalAppScope')){
				$model->withGlobalAppScope();
			}
		});
	}

	/**
	 * @return int
	 */
	public function getGlobalAppId(){
		return $this->request->appId();
	}
}
