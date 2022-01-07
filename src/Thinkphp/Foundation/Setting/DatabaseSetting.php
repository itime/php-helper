<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @copyright (c) 2015~2019 BD All rights reserved.
 * @license       http://www.apache.org/licenses/LICENSE-2.0
 * @author        <657306123@qq.com> LXSEA
 */

namespace Xin\Thinkphp\Foundation\Setting;

use think\facade\App;
use think\facade\Cache;
use think\facade\Config;
use think\Model;
use Xin\Support\Arr;

/**
 * 配置模型
 *
 * @property int    type
 * @property int    sort
 * @property string value
 * @property string extra
 * @property int    group
 */
class DatabaseSetting extends Model {

	/**
	 * 缓存数据的key
	 */
	const CACHE_KEY = 'setting';

	/**
	 * 缓存开放数据的key
	 */
	const CACHE_PUBLIC_KEY = 'setting:public';

	/**
	 * @var string
	 */
	protected $name = 'setting';

	/**
	 * 禁止写入创建时间
	 *
	 * @var bool
	 */
	protected $createTime = false;

	/**
	 * 禁止写入更新时间
	 *
	 * @var bool
	 */
	protected $updateTime = false;

	/**
	 * 插入数据自动完成
	 *
	 * @var array
	 */
	protected $insert = [
		'status' => 1,
	];

	/**
	 * 加载数据库设置信息
	 *
	 * @param array|null $settings
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function load(array $settings = null) {
		// 批量保存配置
		if (is_array($settings)) {
			foreach ($settings as $name => $value) {
				$map = ['name' => $name];

				if (is_array($value)) {
					$value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
				}

				static::where($map)->save([
					'value' => $value,
				]);
			}

			static::updateCache();
		}

		$config = Cache::get(static::CACHE_KEY);
		if (empty($config)) {
			$config = static::resolveCache(static::CACHE_KEY);
		}

		return $config;
	}

	/**
	 * 加载数据库设置信息（公开的数据）
	 *
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function loadPublic() {
		$config = Cache::get(static::CACHE_PUBLIC_KEY);
		if (empty($config)) {
			$config = static::resolveCache(static::CACHE_PUBLIC_KEY, [
				'public' => 1,
			]);
		}

		return $config;
	}

	/**
	 * 获取配置
	 *
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function getSettings() {
		$data = static::loadData();

		$settings = [];
		foreach ($data as $key => $item) {
			$name = explode('.', $item['name'], 2);
			$rootName = isset($name[1]) ? $name[0] : 'web';
			$name = isset($name[1]) ? $name[1] : $name[0];

			if (!isset($settings[$rootName])) {
				$settings[$rootName] = [];
			}

			$settings[$rootName][$name] = $item->value;
			unset($data[$key]);
		}

		return $settings;
	}

	/**
	 * 更新缓存
	 *
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	public static function updateCache() {
		static::resolveCache(static::CACHE_KEY);

		static::resolveCache(static::CACHE_PUBLIC_KEY, [
			'public' => 1,
		]);
	}

	/**
	 * 解析缓存
	 *
	 * @param string $cacheKey
	 * @param array  $where
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected static function resolveCache($cacheKey, $where = []) {
		$data = self::loadData($where);

		$settings = [];
		foreach ($data as $key => $item) {
			$name = $item['name'];
			if (!strpos($name, '.')) {
				$name = 'web.' . $name;
			}

			$settings[$name] = $item->value;

			unset($data[$key]);
		}

		Cache::set($cacheKey, $settings);

		return $settings;
	}

	/**
	 * 加载数据
	 *
	 * @param array $where
	 * @return array|\think\Collection|\Xin\Thinkphp\Foundation\Setting\DatabaseSetting[]
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected static function loadData($where = []) {
		return static::field('type,name,value')
			->where('status', 1)
			->where($where)
			->select();
	}

	/**
	 * 刷新配置到 think\Config 实例中
	 *
	 * @throws \ReflectionException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public static function refreshThinkConfig() {
		/** @var \think\Config $config */
		$thinkConfig = App::get('config');

		$configRef = new \ReflectionProperty($thinkConfig, 'config');
		$configRef->setAccessible(true);
		$globalConfig = $configRef->getValue($thinkConfig);

		$config = DatabaseSetting::load();
		foreach ($config as $key => $value) {
			Arr::set($globalConfig, $key, $value);
		}

		$configRef->setValue($thinkConfig, $globalConfig);
	}

	/**
	 * 获取分组
	 *
	 * @return array
	 * @throws \Xin\Thinkphp\Foundation\Setting\InvalidConfigureException
	 */
	public static function getGroupList() {
		$groups = Config::get('web.config_group_list');

		if (empty($groups)) {
			throw new InvalidConfigureException(
				"请手动配置 settings 数据表 ‘config_group_list’标识"
			);
		}

		if (!is_array($groups)) {
			throw new InvalidConfigureException(
				'获取配置分组数据格式异常！'
			);
		}

		return $groups;
	}

	/**
	 * 获取数据分组
	 *
	 * @return mixed
	 * @throws \Xin\Thinkphp\Foundation\Setting\InvalidConfigureException
	 */
	protected function getGroupTextAttr() {
		$groups = static::getGroupList();
		$group = $this->getData('group');

		return isset($groups[$group]) ? $groups[$group] : "无";
	}

	/**
	 * 获取分组
	 *
	 * @return array
	 * @throws \Xin\Thinkphp\Foundation\Setting\InvalidConfigureException
	 */
	public static function getTypeList() {
		$types = Config::get('web.config_type_list');

		if (empty($types)) {
			throw new InvalidConfigureException(
				"请手动配置数据库settings数据表 ‘config_type_list’ 标识。"
			);
		}

		return $types;
	}

	/**
	 * 获取数据类型
	 *
	 * @return string
	 * @throws \Xin\Thinkphp\Foundation\Setting\InvalidConfigureException
	 */
	protected function getTypeTextAttr() {
		$types = static::getTypeList();
		$type = $this->getData('type');

		return isset($types[$type]) ? $types[$type] : "无";
	}

	/**
	 * 获取扩展配置信息
	 *
	 * @param $string
	 * @return array
	 */
	protected function getExtraAttr($string) {
		$type = $this->getOrigin('type');

		if ($type == 'object') {
			$result = json_decode($string, true);
			if ($result === null) {
				$result = [];
			}

			$values = $this->getAttr('value');
			foreach ($result as &$item) {
				$key = $item['name'];
				if (isset($values[$key])) {
					$item['value'] = $values[$key];
				} elseif (!isset($item['value'])) {
					$item['value'] = '';
				}
			}
			unset($item);

			return $result;
		}

		return Arr::parse($string);
	}

	/**
	 * 根据配置类型解析配置
	 *
	 * @param string $val
	 * @return mixed
	 */
	protected function getValueAttr($val) {
		$type = $this->getData('type');

		if ($type == 'array') {
			return Arr::parse($val);
		} elseif ($type == 'switch') {
			return (int)$val;
		} elseif ($type == 'object') {
			return json_decode($val, true);
		}

		return $val;
	}

	/**
	 * 数据写入后
	 */
	public static function onAfterWrite() {
		static::updateCache();
	}

	/**
	 * 数据删除后
	 */
	public static function onAfterDelete() {
		static::updateCache();
	}

	/**
	 * 安装配置
	 *
	 * @param array $settings
	 */
	public static function setup($settings) {
		$keys = array_keys($settings);
		$existKeys = static::where('name', 'in', $keys)->column('name');
		$attachKeys = array_diff($keys, $existKeys);
		$detachKeys = array_diff($existKeys, $keys);
		$updateKeys = array_diff($existKeys, $detachKeys);

		if (!empty($attachKeys)) {
			foreach ($attachKeys as $attachKey) {
				$setting = $settings[$attachKey];
				static::create([
					'title' => $setting['title'],
					'name' => $attachKey,
					'value' => isset($setting['value']) ? $setting['value'] : '',
					'type' => isset($setting['type']) ? $setting['type'] : 'string',
					'extra' => isset($setting['extra']) ? $setting['type'] : '',
					'group' => isset($setting['group']) ? $setting['group'] : '',
					'sort' => isset($setting['sort']) ? $setting['sort'] : 0,
					'system' => 0,
					'display' => isset($setting['display']) ? $setting['display'] : 1,
				]);
			}
		}

		if (!empty($updateKeys)) {
			foreach ($updateKeys as $updateKey) {
				static::update([
					'type' => isset($setting['type']) ? $setting['type'] : 'string',
					'extra' => isset($setting['extra']) ? $setting['type'] : '',
					'group' => isset($setting['group']) ? $setting['group'] : '',
					'display' => isset($setting['display']) ? $setting['display'] : 1,
				], [
					'name' => $updateKey,
				]);
			}
		}

		if (!empty($detachKeys)) {
			static::unsetup($detachKeys);
		}
	}

	/**
	 * 取消安装配置
	 *
	 * @param array $keys
	 * @return bool
	 */
	public static function unsetup($keys) {
		return static::where('name', 'in', $keys)->delete();
	}

}
