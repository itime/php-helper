<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Filesystem;

use League\Flysystem\FilesystemInterface as BaseFilesystemInterface;
use think\Service;
use Xin\Filesystem\FilesystemInterface;

class FilesystemServiceProvider extends Service {

	/**
	 * 注册对象存储提供者
	 */
	public function register() {
		$this->app->bind([
			'filesystem' => FilesystemInterface::class,
			BaseFilesystemInterface::class => FilesystemInterface::class,
			FilesystemInterface::class => function () {
				return new FilesystemManager($this->app);
			},
		]);
	}

}
