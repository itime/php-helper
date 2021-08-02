<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Setting;

use think\Console;
use Xin\Support\Arr;
use Xin\Thinkphp\Foundation\ServiceProvider;
use Xin\Thinkphp\Foundation\Setting\Command\Clear;
use Xin\Thinkphp\Foundation\Setting\Command\Show;
use Xin\Thinkphp\Foundation\Setting\Command\Update;

class SettingServiceProvider extends ServiceProvider{

	/**
	 * @inheritDoc
	 */
	public function register(){
		Console::starting(function(Console $console){
			$console->addCommands([
				Show::class,
				Clear::class,
				Update::class,
			]);
		});
	}

	/**
	 * @inheritDoc
	 */
	public function boot(){
		if($this->app->runningInConsole()){
			$this->load();
		}else{
			$this->app->event->listen('HttpRun', function(){
				$this->load();
			});
		}
	}

	/**
	 * 加载配置信息
	 *
	 * @throws \ReflectionException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function load(){
		$configRef = new \ReflectionProperty($this->app->config, 'config');
		$configRef->setAccessible(true);
		$globalConfig = $configRef->getValue($this->app->config);

		$config = DatabaseSetting::load();
		foreach($config as $key => $value){
			Arr::set($globalConfig, $key, $value);
			// $this->app->config->set($value, $key);
		}

		$configRef->setValue($this->app->config, $globalConfig);
	}

}
