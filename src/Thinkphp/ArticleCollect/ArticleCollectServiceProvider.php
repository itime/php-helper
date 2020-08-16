<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @copyright (c) 2015~2019 BD All rights reserved.
 * @license       http://www.apache.org/licenses/LICENSE-2.0
 * @author        <657306123@qq.com> LXSEA
 */

namespace Xin\Thinkphp\ArticleCollect;

use think\facade\Validate;
use Xin\articlecollect\ArticleCollect;
use Xin\articlecollect\ArticleCollectException;
use Xin\Thinkphp\hint\facade\Hint;
use Xin\Thinkphp\provider\ServiceProvider;

/**
 * 初始化应用
 */
class ArticleCollectServiceProvider extends ServiceProvider{

	/**
	 * 注册服务
	 */
	public function register(){
	}

	/**
	 * 启动
	 */
	public function boot(){
		// 注册文章抓取路由
		$this->route->get('article/collect', function(){
			$url = $this->request->param('url', '', 'trim');
			if(empty($url) || !Validate::is($url, 'url')){
				Hint::outputError('不是一个有效的url！', 400);
			}

			try{
				$result = ArticleCollect::url($url);
				return Hint::result($result);
			}catch(ArticleCollectException $e){
				return Hint::error($e->getMessage(), $e->getCode());
			}catch(\LogicException $e){
				return Hint::error($e->getMessage(), $e->getCode());
			}
		});
	}

}
