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
		$config = $this->config->get('filesystem');
		
		$fs = new FilesystemManager($config);
		thinkphp60_if(function() use ($fs){
			$this->app->bind([
				'NewFilesystem'                => $fs,
				FilesystemInterface::class     => $fs,
				BaseFilesystemInterface::class => $fs,
			]);
		}, function() use ($fs){
			$this->app->bindTo('NewFilesystem', $fs);
			$this->app->bindTo(FilesystemInterface::class, $fs);
			$this->app->bindTo(BaseFilesystemInterface::class, $fs);
		});
	}
}
