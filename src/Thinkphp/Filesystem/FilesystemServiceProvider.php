<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Filesystem;

use xin\oss\ObjectStorage;
use Xin\Thinkphp\provider\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider{

	/**
	 * 注册对象存储提供者
	 */
	public function register(){
		$driver = $this->config->get('filesystem.driver');
		$drivers = $this->config->get('filesystem.drivers');

		if(empty($drivers) || empty($driver)){
			return;
		}

		$oss = ObjectStorage::factory($driver, $drivers[$driver]);

		$this->app->bindTo('oss', $oss);
		$this->app->bindTo('xin\oss\ObjectStorageInterface', $oss);
	}
}
