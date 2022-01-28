<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

use Opis\Closure\SerializableClosure;
use think\Service;
use Xin\Support\Encrypter;
use Xin\Support\Str;

class EncryptionServiceProvider extends Service
{

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerEncrypter();
		$this->registerOpisSecurityKey();
	}

	/**
	 * Register the encrypter.
	 *
	 * @return void
	 */
	protected function registerEncrypter()
	{
		$this->app->bind('encrypter', function () {
			$config = $this->app->config->get('app');

			return new Encrypter($this->parseKey($config), $config['cipher']);
		});
	}

	/**
	 * Configure Opis Closure signing for security.
	 *
	 * @return void
	 */
	protected function registerOpisSecurityKey()
	{
		$config = $this->app->config->get('app');

		if (!class_exists(SerializableClosure::class) || empty($config['key'])) {
			return;
		}

		SerializableClosure::setSecretKey($this->parseKey($config));
	}

	/**
	 * Parse the encryption key.
	 *
	 * @param array $config
	 * @return string
	 */
	protected function parseKey(array $config)
	{
		if (Str::startsWith($key = $this->key($config), $prefix = 'base64:')) {
			$key = base64_decode(Str::after($key, $prefix));
		}

		return $key;
	}

	/**
	 * Extract the encryption key from the given configuration.
	 *
	 * @param array $config
	 * @return string
	 * @throws \RuntimeException
	 */
	protected function key(array $config)
	{
		if (!isset($config['key']) || empty($config['key'])) {
			throw new \RuntimeException(
				'No application encryption key has been specified.'
			);
		}

		return $config['key'];
	}

}
