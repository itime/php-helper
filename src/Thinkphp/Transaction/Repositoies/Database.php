<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Transaction\Repositoies;

use Xin\Bus\Transaction\Contracts\Repository;
use Xin\Bus\Transaction\Exceptions\TransactionNotFoundException;
use Xin\Thinkphp\Transaction\DatabaseTransaction;
use Xin\Thinkphp\Transaction\Transactions\Database as TransactionDatabase;

class Database implements Repository
{

	public function create($attributes = [])
	{
		return $this->newTransaction(
			DatabaseTransaction::create($attributes)
		);
	}

	public function exist($id)
	{
		return DatabaseTransaction::where('id', $id)->find() != null;
	}

	public function existByNumber($number)
	{
		return DatabaseTransaction::where('no', $number)->find() != null;
	}

	public function fromId($id)
	{
		$data = DatabaseTransaction::where('id', $id)->find();

		if (empty($data)) {
			throw new TransactionNotFoundException();
		}

		return $this->newTransaction($data);
	}

	public function fromNumber($number)
	{
		$data = DatabaseTransaction::where('no', $number)->find();

		if (empty($data)) {
			throw new TransactionNotFoundException();
		}

		return $this->newTransaction($data);
	}

	protected function newTransaction(DatabaseTransaction $databaseTransaction)
	{
		return new TransactionDatabase(
			$databaseTransaction
		);
	}

}
