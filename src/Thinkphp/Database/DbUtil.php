<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Database;

use think\db\exception\PDOException;
use think\facade\Db;

class DbUtil
{

	/**
	 * @param callable $callback
	 * @return void
	 * @throws PDOException
	 */
	public static function call(callable $callback)
	{
		try {
			return $callback();
		} catch (PDOException $e) {
			$data = $e->getData();
			if (isset($data['PDO Error Info']) && $pdoErrorInfo = $data['PDO Error Info']) {
				// Numeric value out of range: 0 Out of range value for col
				if ($pdoErrorInfo['SQLSTATE'] == 22003) {
					return;
				}
			}

			throw $e;
		}
	}

	/**
	 * @return void
	 */
	public static function dumpSqls()
	{
		Db::listen(function ($sql) {
			dump($sql);
		});
	}

}
