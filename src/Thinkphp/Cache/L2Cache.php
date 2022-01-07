<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Cache;

use think\App;
use think\cache\Driver;

class L2Cache extends Driver {

	/**
	 * @var string[]
	 */
	protected $config = [
		'table' => 'cache',
	];

	/**
	 * @var \think\Cache
	 */
	protected $cache;

	/**
	 * @var \think\Db
	 */
	protected $db;

	/**
	 * @var \think\db\Query
	 */
	protected $tableInstance;

	/**
	 * L2Cache constructor.
	 *
	 * @param \think\App $app
	 * @param array      $config
	 */
	public function __construct(App $app, array $config) {
		$this->config = array_merge($this->config, $config);

		$this->cache = $app['cache'];
		$this->db = $app['db'];
	}

	/**
	 * @inheritDoc
	 */
	public function inc(string $name, int $step = 1) {
		$this->cache->inc($name, $step);
		$this->db()->where(['key' => $name])->inc('value', $step);
	}

	/**
	 * @inheritDoc
	 */
	public function dec(string $name, int $step = 1) {
		$this->cache->dec($name, $step);
		$this->db()->where(['key' => $name])->dec('value', $step);
	}

	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DbException
	 */
	public function clearTag(array $keys) {
		$this->cache->clearTag($keys);
		$this->db()->whereIn('tag', $keys)->delete();
	}

	/**
	 * @inheritDoc
	 */
	public function get($key, $default = null) {
		$value = $this->cache->get($key, null);

		if ($value !== null) {
			return $value;
		}

		/** @var \stdClass $data */
		$data = $this->db()->where('key', $key)->find();

		if (is_null($data) || $this->isExpired($data['expire_time'])) {
			return $default;
		}

		$type = $data['type'];
		$value = $data['value'];

		if ('array' == $type) {
			$value = json_decode($value, true);
		} elseif ('object' == $type || 'resource' == $type || 'unknown type' == $type) {
			$value = unserialize($value);
		}

		$this->cache->set($key, $value);

		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function set($key, $value, $ttl = null) {
		if (is_null($key)) return false;

		$this->cache->set($key, $value, $ttl);

		if (is_null($ttl)) {
			$expiresTime = 0;
		} elseif (!is_int($ttl)) {
			$expiresTime = $this->getExpireTime($ttl);
		} else {
			$expiresTime = time() + $ttl;
		}

		$type = gettype($value);
		if ('array' == $type) {
			$value = json_encode($value, JSON_UNESCAPED_UNICODE);
		} elseif ('object' == $type || 'resource' == $type || 'unknown type' == $type) {
			$value = serialize($value);
		}

		$this->db()->replace(true)->insert([
			'key' => $key,
			'value' => $value,
			'type' => $type,
			'expire_time' => $expiresTime,
		]);

		return true;
	}

	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DbException
	 */
	public function delete($key) {
		$this->cache->delete($key);
		$this->db()->where(['key' => $key])->delete();
	}

	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DbException
	 */
	public function clear() {
		$this->cache->clear();
		$this->db()->where('id', '<>', 0)->delete();
	}

	/**
	 * @inheritDoc
	 */
	public function has($key) {
		$flag = $this->cache->has($key);

		if (!$flag) {
			$expireTime = $this->db()->where('id', '<>', 0)->value('expire_time', -1);
			$flag = !$this->isExpired($expireTime);
		}

		return $flag;
	}

	/**
	 * 是否在有效期之内
	 *
	 * @param int $expiration
	 * @return bool
	 */
	protected function isExpired($expiration) {
		$expiration = $this->getExpireTime($expiration);

		return $expiration != 0 && $expiration <= time();
	}

	/**
	 * @return \think\Db|\think\db\Query
	 */
	protected function db() {
		if ($this->tableInstance === null) {
			$this->tableInstance = $this->db->name(
				$this->config['table']
			);
		}

		return $this->tableInstance;
	}

}
