<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Hashing;

use Xin\Contracts\Hashing\Hasher;
use Xin\Support\Manager;

class HashManager extends Manager implements Hasher
{

	/**
	 * 获取缓存配置
	 *
	 * @access public
	 * @param null|string $name 名称
	 * @param mixed $default 默认值
	 * @return mixed
	 */
	public function getConfig($name = null, $default = null)
	{
		if (!is_null($name)) {
			return $this->app->config->get('hashing.' . $name, $default);
		}

		return $this->app->config->get('hashing');
	}

	/**
	 * @inheritDoc
	 */
	protected function resolveType($name)
	{
		return $name;
	}

	/**
	 * @inheritDoc
	 */
	protected function resolveConfig($name)
	{
		return $this->getConfig($name);
	}

	/**
	 * Create an instance of the Bcrypt hash Driver.
	 *
	 * @return \Xin\Hashing\BcryptHasher
	 */
	public function createBcryptDriver($config)
	{
		return new BcryptHasher($config);
	}

	/**
	 * Create an instance of the Argon2i hash Driver.
	 *
	 * @return \Xin\Hashing\ArgonHasher
	 */
	public function createArgonDriver($config)
	{
		return new ArgonHasher($config);
	}

	/**
	 * Create an instance of the Argon2id hash Driver.
	 *
	 * @return \Xin\Hashing\Argon2IdHasher
	 */
	public function createArgon2idDriver($config)
	{
		return new Argon2IdHasher($config);
	}

	/**
	 * Get information about the given hashed value.
	 *
	 * @param string $hashedValue
	 * @return array
	 */
	public function info($hashedValue)
	{
		return $this->driver()->info($hashedValue);
	}

	/**
	 * Hash the given value.
	 *
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function make($value, array $options = [])
	{
		return $this->driver()->make($value, $options);
	}

	/**
	 * Check the given plain value against a hash.
	 *
	 * @param string $value
	 * @param string $hashedValue
	 * @param array $options
	 * @return bool
	 */
	public function check($value, $hashedValue, array $options = [])
	{
		return $this->driver()->check($value, $hashedValue, $options);
	}

	/**
	 * Check if the given hash has been hashed using the given options.
	 *
	 * @param string $hashedValue
	 * @param array $options
	 * @return bool
	 */
	public function needsRehash($hashedValue, array $options = [])
	{
		return $this->driver()->needsRehash($hashedValue, $options);
	}

	/**
	 * Get the default driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->getConfig('driver', 'bcrypt');
	}

}
