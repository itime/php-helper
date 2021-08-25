<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Transaction\Transactions;

use Xin\Contracts\Transaction\Transaction as TransactionContract;
use Xin\Thinkphp\Transaction\DatabaseTransaction;

class Database implements TransactionContract{

	/**
	 * @var \Xin\Thinkphp\Transaction\DatabaseTransaction
	 */
	protected $data;

	/**
	 * Transaction constructor.
	 *
	 * @param \Xin\Thinkphp\Transaction\DatabaseTransaction $data
	 */
	public function __construct(DatabaseTransaction $data){
		$this->data = $data;
	}

	public function isWaiting(){
		// TODO: Implement isWaiting() method.
	}

	public function isPending(){
		// TODO: Implement isPending() method.
	}

	public function isComplete(){
		// TODO: Implement isComplete() method.
	}

	public function setWaiting(){
		// TODO: Implement setWaiting() method.
	}

	public function setPending(){
		// TODO: Implement setPending() method.
	}

	public function setComplete(){
		// TODO: Implement setComplete() method.
	}

	public function isError(){
		// TODO: Implement isError() method.
	}

	public function getError(){
		// TODO: Implement getError() method.
	}

	public function setError($error){
		// TODO: Implement setError() method.
	}

	public function getNumber(){
		// TODO: Implement getNumber() method.
	}

	public function getId(){
		// TODO: Implement getId() method.
	}
}
