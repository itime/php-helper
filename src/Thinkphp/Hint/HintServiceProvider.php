<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Hint;

use think\facade\Request;
use Xin\Thinkphp\Provider\ServiceProvider;

/**
 * Class HintServiceProvider
 */
class HintServiceProvider extends ServiceProvider{

	/**
	 * @var callable
	 */
	private static $isApiRequestResolver = null;

	/**
	 * 启动器
	 */
	public function register(){
		$this->app->bindTo('hint', $this->isApiRequest() ? ApiHint::class : WebHint::class);
	}

	/**
	 * @return bool
	 */
	protected function isApiRequest(){
		$isApiRequest = false;

		if(self::$isApiRequestResolver){
			$isApiRequest = call_user_func(self::$isApiRequestResolver, app('request'));
		}

		return $isApiRequest || Request::isAjax() || Request::isJson();
	}

	/**
	 * @param callable $isApiRequestResolver
	 */
	public static function setIsApiRequestResolver($isApiRequestResolver){
		self::$isApiRequestResolver = $isApiRequestResolver;
	}

}
