<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Stat;

use think\App;
use Xin\Contracts\Stat\Repository as StatRepository;
use Xin\Contracts\Stat\StatProvider as StatProviderContract;
use Xin\Support\Time;

class Stat implements StatRepository{

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var \think\Request
	 */
	protected $request;

	/**
	 * @var \think\Cache
	 */
	protected $cache;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var StatProviderContract
	 */
	protected $provider = null;

	/**
	 * Stat constructor.
	 *
	 * @param \think\App           $app
	 * @param array                $config
	 * @param StatProviderContract $provider
	 */
	public function __construct(App $app, array $config = [], StatProviderContract $provider = null){
		$this->app = $app;
		$this->request = $app['request'];
		$this->cache = $app['cache'];

		$this->config = $config;

		$this->provider = $provider
			? $provider
			: new StatProvider(
				$app, $this, $config
			);
	}

	/**
	 * @inheritDoc
	 */
	public function tally($name, $step = 1, array $options = []){
		$statId = $this->resolveStatID($name, $options);

		$this->provider->incById($statId, $step, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function tallyIP(array $options = []){
		if($this->existIp($options)){
			return;
		}

		$referer = $this->request->server('http_referer');
		$userAgent = $this->request->server('http_user_agent');
		$userAgent = substr($userAgent, 0, 500);

		$this->provider->insertIpLog([
			'ip'          => $this->request->ip(),
			'time'        => Time::today()[0],
			'referer'     => $referer ? $referer : '',
			'user_agent'  => $userAgent ? $userAgent : '',
			'create_time' => $this->request->time(),
		], $options);

		$this->tally('ip', 1, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function value($name, $time = null, array $options = []){
		if($time){
			return $this->provider->getValueByTime($name, $time, $options);
		}else{
			$statId = $this->resolveStatID($name, $options);
			return $this->provider->getValueById($statId);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function total($name, array $options = []){
		return $this->provider->getTotal($name, $options);
	}

	/**
	 * 获取统计ID
	 *
	 * @param string $name
	 * @param array  $options
	 * @return int
	 */
	protected function resolveStatID($name, array $options){
		$todayBeginTime = Time::today()[0];
		$cacheKey = $this->provider->getCacheKey($name, $options);

		$id = $this->cache->get($cacheKey);
		if(empty($id)){
			// 查询数据库有没有今天的数据
			$id = $this->provider->getIdByTime($name, $todayBeginTime, $options);

			// 如果存在的话，插入一条数据
			if(!$id){
				$id = $this->provider->insert([
					'name'        => $name,
					'value'       => 0,
					'create_time' => $todayBeginTime,
				], $options);
			}

			$this->cache->set($cacheKey, $id, 24 * 3600);
		}

		return $id;
	}

	/**
	 * IP 是否存在
	 *
	 * @param array $options
	 * @return bool
	 */
	protected function existIp(array $options){
		return $this->provider->getIPIdByTime(
				$this->request->ip(),
				Time::today()[0],
				$options
			) != 0;
	}

}
