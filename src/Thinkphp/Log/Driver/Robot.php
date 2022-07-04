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
use Xin\Robot\RobotManager;
use Xin\Support\Arr;
use Xin\Support\LimitThrottle;

class Robot implements LogHandlerInterface
{

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
	 * @param array $config
	 */
	public function __construct(App $app, array $config)
	{
		$this->app = $app;
		$this->config = $config;
	}

	/**
	 * 保存日志
	 *
	 * @param array $log
	 * @return bool
	 */
	public function save(array $log): bool
	{
		if (!isset($log['error']) || !$this->isAllowSendBotMessage($errCount)) {
			return true;
		}

		$log = substr($log['error'][0], 0, 1024);
		$ip = $this->app->request->server('SERVER_ADDR');
		$clientId = $this->app->request->ip();

		if ($this->app->runningInConsole()) {
			$input = new Input();
			$arguments = array_map(static function (Argument $argument) {
				return $argument->getName();
			}, $input->getArguments());

			$options = array_map(static function (Option $option) {
				return $option->getName();
			}, $input->getOptions());

			$command = array_shift($arguments);
			$info = $command .
				"->args:" . json_encode($arguments) .
				":options:" . json_encode($options);
		} else {
			$info = $this->app->request->method() . " " . $this->app->request->url(true);
		}

		$env = env('app_env');
		$contents = <<<MARKDOWN
<font color="warning">**ERROR ({$env}:[{$clientId}->{$ip}]:10 分钟出现 {$errCount} 次)**</font>
<font color="info">{$info}</font>
<font color="comment">{$log}</font>
MARKDOWN;

		// 机器人发送告警消息
		$this->sendRobotMessage($contents);

		return true;
	}

	/**
	 * 获取机器人Key
	 *
	 * @return string
	 */
	protected function robot()
	{
		if (isset($this->config['robot']) && $this->config['robot']) {
			return $this->config['robot'];
		}

		return null;
	}

	/**
	 * 机器人发送告警消息
	 * @param string $contents
	 * @return void
	 */
	protected function sendRobotMessage($contents)
	{
		try {
			/** @var RobotManager $factory */
			$factory = $this->app->robot;
			$factory->robot($this->robot())->sendMarkdownMessage($contents);
		} catch (\Throwable $e) {
		}
	}

	/**
	 * 是否允许发送消息
	 *
	 * @param int $count
	 * @return bool
	 */
	protected function isAllowSendBotMessage(&$count = 0)
	{
		if (!$this->app->has('robot') || !$this->robot() ||
			!in_array(env('app_env'), $this->getAllowEnvs())
		) {
			return false;
		}

		try {
			$count = $this->getErrorCount();
		} catch (\Exception $e) {
			return false;
		}

		return (bool)LimitThrottle::general(function () use ($count) {
			return $count;
		}, function () {
			return true;
		});
	}

	/**
	 * 获取运行的环境列表
	 * @return array
	 */
	protected function getAllowEnvs()
	{
		return Arr::wrap($this->config['allow_envs'] ?? ['production']);
	}

	/**
	 * 解析当前错误地址错误数量
	 *
	 * @return int
	 */
	protected function getErrorCount()
	{
		$key = $this->getErrorCacheKey();
		$count = $this->app->cache->get($key);

		if ($count === null) {
			$count = 0;
			$this->app->cache->set($key, 1, now()->addMinutes(10));
		} else {
			$this->app->cache->inc($key);
		}

		return ++$count;
	}

	/**
	 * 解析当前错误地址缓存的key
	 *
	 * @return string
	 */
	protected function getErrorCacheKey()
	{
		$url = $this->app->request->url();
		$url = preg_replace('/\&timestamp=\d{0,10}/', '', $url);

		return "error:count:" . md5($url);
	}

}
