<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\facade\Cache;
use Think\Model;
use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Plugin\PluginNotFoundException;
use Xin\Support\Version;

/**
 * Class DatabasePlugin
 *
 * @property-read string name
 * @property-read int install
 * @property-read bool is_new_version
 * @property-read string local_version
 * @property array config
 */
class DatabasePlugin extends Model
{

	// 缓存前缀
	public const CACHE_PREFIX = 'plugin:';

	/**
	 * 插件配置缓存列表
	 *
	 * @var array
	 */
	protected static $configCacheList = [];

	/**
	 * 已被禁用的缓存列表
	 * @var array
	 */
	protected static $disabledCacheList = null;

	/**
	 * @var string
	 */
	protected $name = 'plugin';

	/**
	 * @var array
	 */
	protected $type = [
		'config' => ['json', JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE],
	];

	/**
	 * 插件写入数据库后相关操作
	 *
	 * @param static $model
	 */
	public static function onAfterWrite(self $model): void
	{
		// 检查改变后的数据是否存在config字段，如果存在则更新缓存
		$changeData = $model->getChangedData();

		// 安全更新
		try {
			static::setPluginConfigCache($model->getOrigin('name'), $model->getAttr('config'));
		} catch (\Throwable $e) {
		}
	}

	/**
	 * 获取插件配置
	 *
	 * @param string $plugin
	 * @param mixed $default
	 * @param false $isUpdateCache
	 * @return mixed
	 */
	public static function getPluginConfig($plugin, $default = null, $isUpdateCache = false)
	{
		if (isset(static::$configCacheList[$plugin])) {
			$config = static::$configCacheList[$plugin];
		} else {
			$key = static::getPluginConfigCacheKey($plugin);
			$config = Cache::get($key);

			// 加载数据库数据
			if (!$config) {
				$config = static::where('name', $plugin)->value('config');
				$config = json_decode($config, true);
				static::setPluginConfigCache($plugin, $config);
			}
		}

		if (is_null($config)) {
			$config = $default instanceof \Closure ? $default() : $default;

			static::$configCacheList[$plugin] = $default;

			if ($isUpdateCache) {
				static::setPluginConfigCache($plugin, $config);
			}
		}

		return $config;
	}

	/**
	 * 设置插件配置缓存
	 *
	 * @param string $plugin
	 * @param mixed $config
	 */
	public static function setPluginConfigCache($plugin, $config)
	{
		$key = static::getPluginConfigCacheKey($plugin);

		Cache::set($key, $config instanceof \Closure ? $config() : $config);
	}

	/**
	 * 刷新插件配置缓存
	 *
	 * @param string $plugin
	 */
	public static function refreshPluginConfigCache($plugin)
	{
		$config = static::where('name', $plugin)->value('config');
		if (!$config) {
			return;
		}

		$config = json_decode($config, true);
		static::setPluginConfigCache($plugin, $config);
	}

	/**
	 * 获取插件配置前缀
	 *
	 * @param string $plugin
	 * @return string
	 */
	public static function getPluginConfigCacheKey($plugin)
	{
		return static::CACHE_PREFIX . 'config:' . $plugin;
	}


	/**
	 * 获取已禁用的列表
	 * @return array
	 */
	public static function getPluginDisabledListCache()
	{
		$key = static::getPluginDisabledCacheKey();

		$list = Cache::get($key);

		if ($list === false || $list === null) {
			$list = static::where('status', 0)->column('name');

			Cache::set($key, $list);
		}

		return $list;
	}

	/**
	 * 刷新已禁用列表的缓存
	 * @return array
	 */
	public static function refreshPluginDisabledListCache()
	{
		$key = static::getPluginDisabledCacheKey();
		Cache::delete($key);

		return static::getPluginDisabledListCache();
	}

	/**
	 * @return string
	 */
	public static function getPluginDisabledCacheKey()
	{

		return static::CACHE_PREFIX . 'disabled_list';
	}

	/**
	 * 获取已被禁用的插件列表
	 * @return array
	 */
	public static function getPluginDisabledList()
	{
		if (static::$disabledCacheList === null) {
			static::$disabledCacheList = static::getPluginDisabledListCache();
		}

		return static::$disabledCacheList;
	}

	/**
	 * 检查是否已被禁用
	 * @param string $plugin
	 * @return bool
	 */
	public static function checkPluginDisabled($plugin)
	{
		$plugins = static::getPluginDisabledList();

		return in_array($plugin, $plugins, true);
	}

	/**
	 * 获取本地插件信息
	 *
	 * @param string $attr
	 * @return array|\Xin\Plugin\PluginInfo|null
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function getLocalInfo($attr = null)
	{
		/** @var PluginFactory $pluginFactory */
		$pluginFactory = app(PluginFactory::class);
		try {
			/** @var \Xin\Plugin\PluginInfo $info */
			$info = $pluginFactory->plugin($this->getOrigin('name'));

			if ($attr) {
				return $info->getInfo($attr);
			}

			return $info;
		} catch (PluginNotFoundException $e) {
		}

		return null;
	}

	/**
	 * 获取本地信息
	 *
	 * @return \Xin\Contracts\Plugin\PluginInfo|null
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	protected function getLocalInfoAttr()
	{
		return $this->getLocalInfo();
	}

	/**
	 * 获取本地版本号
	 *
	 * @return \Xin\Contracts\Plugin\PluginInfo|null
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	protected function getLocalVersionAttr()
	{
		return $this->getLocalInfo('version');
	}

	/**
	 * 是否有新版本
	 *
	 * @return bool
	 */
	protected function getIsNewVersionAttr()
	{
		$localVersion = $this->getAttr('local_version');
		if (!$localVersion) {
			return false;
		}

		return Version::lt($this->getOrigin('version'), $localVersion);
	}

}
