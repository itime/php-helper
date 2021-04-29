<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Filesystem;

use League\Flysystem\FilesystemInterface as BaseFilesystemInterface;
use Xin\Filesystem\FilesystemInterface;
use Xin\Thinkphp\Foundation\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider{

	/**
	 * 注册对象存储提供者
	 */
	public function register(){
		thinkphp60_if(function(){
			$this->app->bind([
				'filesystem'                   => BaseFilesystemInterface::class,
				BaseFilesystemInterface::class => FilesystemInterface::class,
				FilesystemInterface::class     => function(){
					return new FilesystemManager($this->app);
				},
			]);
		}, function(){
			$this->app->bindTo('filesystem', BaseFilesystemInterface::class);
			$this->app->bindTo(BaseFilesystemInterface::class, FilesystemInterface::class);
			$this->app->bindTo(FilesystemInterface::class, function(){
				return new FilesystemManager($this->app);
			});
		});
	}
}
