<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Log\Driver;

use think\App;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\contract\LogHandlerInterface;
use Xin\Bot\WeworkBot;
use Xin\Support\LimitThrottle;

class Bot implements LogHandlerInterface{

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * Bot constructor.
	 *
	 * @param \think\App $app
	 * @param array      $config
	 */
	public function __construct(App $app, array $config){
		$this->app = $app;
		$this->config = $config;
	}

	/**
	 * 保存日志
	 *
	 * @param array $log
	 * @return bool
	 */
	public function save(array $log):bool{
		if(!isset($log['error']) || !($botKey = $this->key())
			|| ($env = env('app_env')) !== 'production'
			|| !$this->isAllowSendBotMessage($errCount)){
			return true;
		}

		$log = substr($log['error'][0], 0, 512);
		$ip = $this->app->request->server('SERVER_ADDR');
		if($this->app->runningInConsole()){
			$input = new Input();
			$arguments = array_map(function(Argument $argument){
				return $argument->getName();
			}, $input->getArguments());

			$options = array_map(function(Option $option){
				return $option->getName();
			}, $input->getOptions());

			$command = array_shift($arguments);
			$info = $command.
				"->args:".json_encode($arguments).
				":options:".json_encode($options);
		}else{
			$info = $this->app->request->method()." ".$this->app->request->url(true);
		}

		$contents = <<<MARKDOWN
<font color="warning">**ERROR ({$env}:{$ip}:10 min:{$errCount})**</font>
<font color="info">{$info}</font>
<font color="comment">{$log}</font>
MARKDOWN;

		try{
			$bot = new WeworkBot($botKey);
			$bot->sendMarkdownMessage($contents);
		}catch(\Throwable $e){
			return false;
		}

		return true;
	}

	/**
	 * 获取机器人Key
	 *
	 * @return string
	 */
	protected function key(){
		if(isset($this->config['key']) && $this->config['key']){
			return $this->config['key'];
		}

		return null;
	}

	/**
	 * 是否允许发送消息
	 *
	 * @param int $count
	 * @return bool
	 */
	protected function isAllowSendBotMessage(&$count = 0){
		try{
			$count = $this->resolveErrorCount();
		}catch(\Exception $e){
			return false;
		}

		return (bool)LimitThrottle::general(function() use ($count){
			return $count;
		}, function(){
			return true;
		});
	}

	/**
	 * 解析当前错误地址错误数量
	 *
	 * @return int
	 */
	private function resolveErrorCount(){
		$key = $this->resolveErrorCacheKey();
		$count = $this->app->cache->get($key);

		if($count === null){
			$count = 0;
			$this->app->cache->set($key, 1, now()->addMinutes(10));
		}else{
			$this->app->cache->inc($key);
		}

		return ++$count;
	}

	/**
	 * 解析当前错误地址缓存的key
	 *
	 * @return string
	 */
	private function resolveErrorCacheKey(){
		$url = $this->app->request->url();
		$url = preg_replace('/\&timestamp=\d{0,10}/', '', $url);
		return "error:count:".md5($url);
	}
}
