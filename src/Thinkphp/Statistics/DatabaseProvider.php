<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Statistics;

use think\App;
use Xin\Contracts\Stat\Provider as StatProviderContract;
use Xin\Contracts\Stat\Repository as StatRepository;
use Xin\Support\Time;

class DatabaseProvider implements StatProviderContract
{

	/**
	 * @var \think\Db
	 */
	protected $db;

	/**
	 * @var StatRepository
	 */
	protected $stat;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * StatProvider constructor.
	 *
	 * @param \think\App $app
	 * @param StatRepository $stat
	 * @param array $config
	 */
	public function __construct(App $app, StatRepository $stat, array $config = [])
	{
		$this->app = $app;
		$this->db = $app['db'];

		$this->stat = $stat;
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function getCacheKey($name, array $options = [])
	{
		$todayBeginTime = Time::today()[0];
		$prefix = md5(implode('_', array_keys($options)));

		return "stat_{$name}_{$prefix}_{$todayBeginTime}";
	}

	/**
	 * @inheritDoc
	 */
	public function getIdByTime($name, $time, array $options = [])
	{
		return $this->query()->where(array_merge($options, [
			'name' => $name,
			'create_time' => $time,
		]))->value('id');
	}

	/**
	 * @inheritDoc
	 */
	public function getValueByTime($name, $time = null, array $options = [])
	{
		return $this->query()->where('name', $name)
			->where('create_time', '>', $time - 1)
			->where($options)->sum('value');
	}

	/**
	 * @inheritDoc
	 */
	public function getValueById($id, array $options = [])
	{
		return $this->query()->where('id', $id)->where($options)
			->order('id desc')->value('value');
	}

	/**
	 * @inheritDoc
	 */
	public function incById($id, $step = 1, array $options = [])
	{
		return $this->query()->where('id', $id)->inc('value')->save();
	}

	/**
	 * @inheritDoc
	 */
	public function getTotal($name, array $options = [])
	{
		return $this->query()->where('name', $name)
			->where($options)
			->sum('value');
	}

	/**
	 * @inheritDoc
	 */
	public function insert($data, array $options = [])
	{
		return $this->query()->insertGetId(array_merge($options, $data));
	}

	/**
	 * 获取查询对象
	 *
	 * @return \think\Db|\think\db\Query|\think\Model
	 */
	protected function query()
	{
		if (isset($this->config['model'])) {
			$model = $this->config['model'];

			return new $model();
		}

		return $this->db->name($this->config['table']);
	}

	/**
	 * 获取查询对象
	 *
	 * @return \think\Db|\think\db\Query|\think\Model
	 */
	protected function ipQuery()
	{
		if (isset($this->config['ip_model'])) {
			$model = $this->config['ip_model'];

			return new $model();
		}

		return $this->db->name($this->config['ip_table']);
	}

	/**
	 * @inheritDoc
	 */
	public function getIPIdByTime($ip, $time, array $options = [])
	{
		return $this->ipQuery()->where([
			'ip' => $ip,
			'time' => Time::today()[0],
		])->where($options)->value('id');
	}

	/**
	 * @inheritDoc
	 */
	public function insertIpLog($data, array $options = [])
	{
		return $this->ipQuery()->insertGetId(array_merge($options, $data));
	}

}
