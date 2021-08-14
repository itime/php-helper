<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\Service;
use Xin\Contracts\Hint\Factory as HintFactory;
use Xin\Hint\HintManager;

/**
 * Class HintServiceProvider
 */
class HintServiceProvider extends Service{

	/**
	 * 启动器
	 */
	public function register(){
		$this->app->bind([
			'hint'             => HintFactory::class,
			HintFactory::class => function(){
				$hint = new HintManager;

				$this->registerHintScenes($hint);

				return $hint;
			},
		]);
	}

	/**
	 * 注册提示器场景
	 *
	 * @param \Xin\Hint\HintManager $manager
	 */
	protected function registerHintScenes(HintManager $manager){
		// extend api hint
		$manager->extend('api', function(){
			return $this->app->make(ApiHint::class);
		});

		// extend web hint
		$manager->extend('web', function(){
			return $this->app->make(WebHint::class);
		});

		// set auto bind hint
		$manager->setAutoResolver(function(){
			return $this->getScene();
		});
	}

	/**
	 * @return string
	 */
	protected function getScene(){
		return $this->isApiRequest() ? "api" : "web";
	}

	/**
	 * @return bool
	 */
	protected function isApiRequest(){
		return $this->app->request->isAjax() ||
			$this->app->request->isJson() ||
			$this->app->http->getName() === 'api';
	}

}
