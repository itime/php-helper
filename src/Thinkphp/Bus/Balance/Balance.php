<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Bus\Balance;

use think\db\BaseQuery as Query;
use think\exception\ValidateException;
use think\facade\App;
use think\facade\Db;
use Xin\Bus\Balance\BalanceModifyException;
use Xin\Contracts\Bus\Balance\BalanceRepository;
use Xin\Support\Arr;
use Xin\Support\Str;

class Balance implements BalanceRepository{

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * Balance constructor.
	 *
	 * @param array $config
	 */
	public function __construct($config = []){
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function recharge($userId, $amount, $remark = '', $attributes = []){
		$query = $this->newQuery()->where('id', $userId)->inc($this->field(), $amount);

		$fieldTotal = $this->fieldTotal();
		if($fieldTotal){
			$query = $query->inc($fieldTotal, $amount);
		}

		$result = $query->update($attributes);
		if(!$result){
			throw new \LogicException("余额变更失败！", 40100);
		}

		$logData = $this->insertLog($userId, 1, $amount, $remark, $attributes);

		$logData['current'] = $value = $this->value($userId);

		$this->triggerEvent($logData);

		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function consume($userId, $amount, $remark = '', $attributes = []){
		$value = $this->newQuery()->where('id', $userId)->value($this->field());
		if($value < $amount){
			throw new ValidateException("余额不足！");
		}

		$result = $this->newQuery()->where('id', $userId)->dec($this->field(), $amount)->update($attributes);
		if(!$result){
			throw new BalanceModifyException("余额变更失败！", 40200);
		}

		$logData = $this->insertLog($userId, 0, $amount, $remark, $attributes);
		$logData['current'] = $value - $amount;

		$this->triggerEvent($logData);

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function value($userId){
		return (float)$this->newQuery()->where('id', $userId)->value($this->field(), 0);
	}

	/**
	 * 插入记录
	 *
	 * @param int    $userId
	 * @param string $type
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return array
	 */
	protected function insertLog($userId, $type, $amount, $remark = '', $attributes = []){
		$data = array_merge($attributes, [
			'log_no'      => Str::makeOrderSn(),
			'user_id'     => $userId,
			'type'        => $type,
			'amount'      => $amount,
			'remark'      => $remark,
			'create_time' => time(),
		]);

		$this->logQuery()->insert($data);

		return $data;
	}

	/**
	 * 获取余额字段
	 *
	 * @return string
	 */
	protected function field(){
		return Arr::get($this->config, 'field', 'balance');
	}

	/**
	 * 获取累计余额字段（累计余额字段只增不减）
	 *
	 * @return string
	 */
	protected function fieldTotal(){
		return Arr::get($this->config, 'field_total', null);
	}

	/**
	 * 触发事件
	 *
	 * @param array $logData
	 */
	protected function triggerEvent($logData){
		$eventClass = Arr::get($this->config, 'event');
		if(!$eventClass){
			return;
		}

		$logData['field'] = $this->field();;

		$app = App::getInstance();
		$event = $app->invokeClass($eventClass, [$logData]);
		$app->event->trigger($event);
	}

	/**
	 * 获取模型查询实例
	 *
	 * @return Query
	 */
	protected function newQuery(){
		$modelClass = Arr::get($this->config, 'model');
		if(!$modelClass){
			throw new \RuntimeException("balance model not defined.");
		}

		return (new $modelClass)->db();
	}

	/**
	 * @return \think\Db|\think\db\Query|\think\facade\Db
	 */
	protected function logQuery(){
		$config = Arr::get($this->config, 'log');
		if($config['type'] === 'model'){
			$modelClass = $config['model'];
			return (new $modelClass)->db();
		}

		return Db::name($config['table']);
	}
}
