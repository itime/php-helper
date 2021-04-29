<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use Xin\Contracts\Hint\Factory as HintFactory;
use Xin\Hint\HintManager;
use Xin\Support\Reflect;
use Xin\Thinkphp\Foundation\ServiceProvider;

/**
 * Class HintServiceProvider
 */
class HintServiceProvider extends ServiceProvider{

	/**
	 * 启动器
	 */
	public function register(){
		$hint = new HintManager;

		// extend api hint
		$hint->extend('api', function(){
			return $this->app->make(ApiHint::class);
		});

		// extend web hint
		$hint->extend('web', function(){
			return $this->app->make(WebHint::class);
		});

		// set auto bind hint
		$hint->setAutoResolver(function(){
			return $this->autoHint();
		});

		if(Reflect::VISIBLE_PUBLIC === Reflect::methodVisible($this->app, 'bindTo')){
			$this->app->bindTo(HintFactory::class, 'hint');
			$this->app->bindTo('hint', $hint);
		}else{
			$this->app->bind(HintFactory::class, 'hint');
			$this->app->bind('hint', $hint);
		}
	}

	/**
	 * @return string
	 */
	protected function autoHint(){
		return $this->isApiRequest() ? "api" : "web";
	}

	/**
	 * @return bool
	 */
	protected function isApiRequest(){
		return $this->request->isAjax() || $this->request->isJson();
	}

}
