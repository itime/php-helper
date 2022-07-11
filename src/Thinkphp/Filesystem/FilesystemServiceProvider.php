<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Filesystem;

use League\Flysystem\FilesystemInterface as BaseFilesystemInterface;
use think\Service;
use Xin\Contracts\Auth\AuthVerifyType;
use Xin\Contracts\Uploader\Factory as UploaderFactory;
use Xin\Filesystem\FilesystemInterface;
use Xin\Foundation\Filesystem\StorageBuilder;
use Xin\Support\Arr;
use Xin\Uploader\UploadManager;

class FilesystemServiceProvider extends Service
{

	/**
	 * 注册对象存储提供者
	 */
	public function register()
	{
		$this->registerFilesystem();

		$this->registerUploadManager();
	}

	protected function registerFilesystem()
	{
		$this->app->bind([
			'filesystem' => FilesystemInterface::class,
			BaseFilesystemInterface::class => FilesystemInterface::class,
			FilesystemInterface::class => function () {
				$filesystem = new FilesystemManager($this->app->config->get('filesystem'));
				$filesystem->setContainer($this->app);
				return $filesystem;
			},
		]);

		StorageBuilder::setDefaultFilesystemCreator(static function () {
			return app('filesystem')->disk();
		});
	}

	protected function registerUploadManager()
	{
		$this->app->bind([
			'uploader' => UploaderFactory::class,
			UploaderFactory::class => UploadManager::class,
			UploadManager::class => function () {
				$uploadManager = new UploadManager(function ($name) {
					$disk = $this->app->filesystem->disk($name);
					if ($disk instanceof FilesystemAdapter) {
						return $disk->getFilesystem();
					}

					return $disk;
				}, [$this, 'resolveUploaderProvider'], $this->app->config->get('upload'));

				$uploadManager->validateFileUsing([$this, 'validateFile']);

				$uploadManager->setContainer($this->app);

				return $uploadManager;
			},
		]);
	}

	/**
	 * @param string $class
	 * @param string $scene
	 * @param array $config
	 * @return mixed
	 */
	public function resolveUploaderProvider($class, $scene, $config)
	{
		$provider = $this->app->make($class, [
			'config' => $config
		]);

		if (method_exists($provider, 'setUser')) {
			$provider->setUser($this->app->request->user(null, null, AuthVerifyType::NOT));
		}

		return $provider;
	}

	/**
	 * 验证文件合法性
	 * @param \SplFileInfo $file
	 * @param array $config
	 * @return bool
	 */
	public function validateFile(\SplFileInfo $file, $config)
	{
		$rules = [];
		if ($size = Arr::get($config, 'size')) {
			$rules['fileSize'] = $size;
		}

		if ($extensions = Arr::get($config, 'extensions')) {
			$rules['fileExt'] = $extensions;
		}

		if ($mimes = Arr::get($config, 'mimes')) {
			$rules['fileMime'] = $mimes;
		}

		return validate([
			'file' => $rules,
		], [], false, true)->rule([], [
			'file' => '文件',
		])->check([
			'file' => $file,
		]);
	}

}
