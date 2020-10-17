<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Setting;

use think\Console;
use Xin\Thinkphp\Foundation\ServiceProvider;
use Xin\Thinkphp\Setting\Command\Clear;
use Xin\Thinkphp\Setting\Command\Show;
use Xin\Thinkphp\Setting\Command\Update;

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
		$this->app->event->listen('HttpRun', function(){
			$config = DatabaseSetting::load();
			foreach($config as $key => $value){
				$this->app->config->set($value, $key);
			}
		});
	}
	
}
