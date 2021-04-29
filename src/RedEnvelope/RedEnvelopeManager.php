<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\RedEnvelope;

use Xin\Contracts\RedEnvelope\Factory as FactoryContract;
use Xin\Support\Str;

class RedEnvelopeManager implements FactoryContract{

	/**
	 * @var array
	 */
	protected $config = [
		'warp' => false,
	];

	/**
	 * @var array
	 */
	protected $generatorProviders = [];

	/**
	 * @var array
	 */
	protected $receiverProviders = [];

	/**
	 * RedEnvelopeManager constructor.
	 *
	 * @param array $config
	 */
	public function __construct($config){
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function generate(array $options){
		$type = isset($options['type']) ? $options['type'] : 'average';
		$generator = $this->resolveGenerator($type, $options);

		if(is_callable($generator)){
			$result = $generator($options);
		}else{
			$result = $generator->generate();
		}

		return $this->castGenerateLog(
			$type, $result
		);
	}

	/**
	 * 解析红包生成器
	 *
	 * @param string $type
	 * @param array  $options
	 * @return \Xin\Contracts\RedEnvelope\Generator
	 */
	protected function resolveGenerator($type, $options){
		if(isset($this->generatorProviders[$type])){
			return $this->generatorProviders[$type];
		}

		$class = $this->resolveClass($type, "Generator");

		if(!class_exists($class)){
			throw new \RuntimeException(
				"RedEnvelope [{$type}] generator not exist."
			);
		}

		return new $class($options);
	}

	/**
	 * @inheritDoc
	 */
	public function hasGenerator($type){
		$class = $this->resolveClass($type, "Generator");

		return class_exists($class);
	}

	/**
	 * 转化红包生成记录
	 *
	 * @param string $type
	 * @param array  $redEnvelopeLogs
	 * @return array[]
	 */
	protected function castGenerateLog($type, array $redEnvelopeLogs){
		return array_map(function($money) use ($type){
			return [
				'redenvelope_type' => $type,
				'order_sn'         => Str::makeOrderSn(),
				'money'            => $money,
				'transaction_id'   => '',

				'user_id' => 0,

				'give_status'     => 0,
				'give_time'       => 0,
				'cash_out_status' => 0,
				'cash_out_time'   => 0,
				'openid'          => '',

				'extra' => '',
			];
		}, $redEnvelopeLogs);
	}

	/**
	 * 扩展红包生成器
	 *
	 * @param string $name
	 * @param mixed  $provider
	 */
	public function defineGenerator($name, $provider){
		$this->generatorProviders[$name] = $provider;
	}

	/**
	 * @inheritDoc
	 */
	public function receive(array $options){
		$type = isset($options['type']) ? $options['type'] : 'average';
		$receiver = $this->resolveReceiver($type, $options);

		if(is_callable($receiver)){
			$result = $receiver($options);
		}else{
			$result = $receiver->receive();
		}

		return $this->castGenerateLog(
			$type, $result
		);
	}

	/**
	 * @inheritDoc
	 */
	public function hasReceiver($type){
		$class = $this->resolveClass($type, "Receiver");

		return class_exists($class);
	}

	/**
	 * 解析红包生成器
	 *
	 * @param string $type
	 * @param array  $options
	 * @return \Xin\Contracts\RedEnvelope\Receiver
	 */
	protected function resolveReceiver($type, $options){
		if(isset($this->receiverProviders[$type])){
			return $this->receiverProviders[$type];
		}

		$class = $this->resolveClass($type, "Receiver");

		if(!class_exists($class)){
			throw new \RuntimeException(
				"RedEnvelope [{$type}] receiver not exist."
			);
		}

		return new $class($options);
	}

	/**
	 * 扩展红包领取器
	 *
	 * @param string $name
	 * @param mixed  $provider
	 */
	public function defineReceiver($name, $provider){
		$this->receiverProviders[$name] = $provider;
	}

	/**
	 * 解析红包相关类路径
	 *
	 * @param string $type
	 * @param string $suffix
	 * @return string
	 */
	protected function resolveClass($type, $suffix){
		if(strpos($type, "\\") !== 0){
			$class = "\\Xin\\RedEnvelope\\".Str::studly($type)."\\".$suffix;
		}else{
			$class = $type;
		}

		return $class;
	}
}
