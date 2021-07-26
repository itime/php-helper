<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Notifications;

use Xin\Contracts\Notifications\Dispatcher as DispatcherContract;
use Xin\Contracts\Notifications\Factory as FactoryContract;
use Xin\Thinkphp\Foundation\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider{

	/**
	 * Boot the application services.
	 *
	 * @return void
	 */
	public function boot(){
		$this->loadViewsFrom(__DIR__.'/resources/views', 'notifications');

		if($this->app->runningInConsole()){
			$this->publishes([
				__DIR__.'/resources/views' => $this->app->resourcePath('views/vendor/notifications'),
			], 'laravel-notifications');
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register(){
		$this->app->singleton(ChannelManager::class, function($app){
			return new ChannelManager($app);
		});

		$this->app->alias(
			ChannelManager::class, DispatcherContract::class
		);

		$this->app->alias(
			ChannelManager::class, FactoryContract::class
		);
	}
}
