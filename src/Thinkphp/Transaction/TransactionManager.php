<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Transaction;

use Xin\Contracts\Transaction\Factory;
use Xin\Support\Arr;
use Xin\Support\Manager;

class TransactionManager extends Manager implements Factory {

	/**
	 * @var array
	 */
	protected $config;

	public function __construct($app, array $config) {
		parent::__construct($app);
		$this->config = $config;
	}

	public function transaction($name = null) {
		// TODO: Implement transaction() method.
	}

	public function getDefaultDriver() {
		return Arr::get($this->config, 'default', 'database');
	}

	protected function resolveType($name) {
		return Arr::get($this->config, "transaction.{$name}.type", "database");
	}

	protected function resolveConfig($name) {
		return Arr::get($this->config, "transaction.{$name}");
	}

}
