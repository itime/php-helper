<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Bus;

use Guzzle\Service\Exception\ValidationException;
use think\facade\Db;
use think\Model;
use Xin\Bus\Balance\SceneEnum;
use Xin\Contracts\Bus\BalanceRepository;
use Xin\Support\Str;

class Balance extends Model implements BalanceRepository{
	
	/**
	 * @var string
	 */
	protected $balanceKey = 'balance';
	
	/**
	 * @var array
	 */
	protected $config = [];
	
	/**
	 * Balance constructor.
	 *
	 * @param string $name
	 * @param array  $config
	 */
	public function __construct($name, $config = []){
		$this->name = $name;
		$this->config = $config;
		
		parent::__construct([]);
	}
	
	/**
	 * 余额消费场景
	 *
	 * @return string
	 */
	protected function getSceneTextAttr(){
		$val = $this->getOrigin('scene');
		return SceneEnum::data()[$val]['title'];
	}
	
	/**
	 * @inheritDoc
	 */
	public function getBalanceByUserId($userId){
		return $this->where('id', $userId)->value(
			$this->balanceKey
		);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getBalanceListByType($userId, $type = null, array $options = []){
		$paginate = $options['paginate'] ?? [];
		unset($options['paginate']);
		
		$query = $this->logQuery()->where([
			'user_id' => $userId,
		])->when($type !== null, ['type' => $type,])
			->order('id desc');
		
		return $this->resolveOptions($query, $options)->paginate($paginate);
	}
	
	/**
	 * @return \think\Db|\think\db\Query|\think\facade\Db
	 */
	protected function logQuery(){
		$config = $this->config['balance_log'];
		if($config['type'] === 'model'){
			$class = $config['model'];
			
			return new $class();
		}
		
		return Db::name($config['table']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function recharge($userId, $amount, $remark = '', $attributes = []){
		$result = $this->where('id', $userId)->inc($this->balanceKey, $amount)->update($attributes);
		if(!$result){
			throw new \LogicException("余额变更失败！");
		}
		
		if($this->insertLog($userId, 1, $amount, $remark, $attributes) === false){
			throw new \LogicException("余额变更失败！");
		}
		
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function consume($userId, $amount, $remark = '', $attributes = []){
		$balance = $this->balanceValue($userId);
		if($balance < $amount){
			throw new ValidationException("余额不足！");
		}
		
		$result = $this->where('id', $userId)->dec($this->balanceKey, $amount)->update($attributes);
		if(!$result){
			throw new \LogicException("余额变更失败！");
		}
		
		if($this->insertLog($userId, 0, $amount, $remark, $attributes) === false){
			throw new \LogicException("余额变更失败！");
		}
		
		return true;
	}
	
	/**
	 * 获取用户的余额
	 *
	 * @param int $userId
	 * @return float
	 */
	public function balanceValue($userId){
		return (float)$this->where('id', $userId)->value($this->balanceKey, 0);
	}
	
	/**
	 * 插入记录
	 *
	 * @param int    $userId
	 * @param string $type
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return int|string
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
		
		return $this->logQuery()->insert($data);
	}
	
}
