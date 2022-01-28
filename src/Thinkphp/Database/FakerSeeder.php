<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Database;

use think\facade\Env;

/**
 * Class Seeder
 *
 * @mixin \think\migration\Seeder
 */
trait FakerSeeder
{

	/**
	 * 构建数据
	 *
	 * @param callable $callback
	 * @param int $num
	 * @param string $locale
	 */
	protected function factory($callback, $num = 0, $locale = 'zh_CN')
	{
		$faker = $this->faker(!empty($locale) ? $locale : Env::get('faker_locale', 'zh_CN'));

		if ($num < 1) {
			$num = rand(1, 100);
		}

		for ($i = 0; $i < $num; $i++) {
			if (call_user_func($callback, $faker) === false) {
				break;
			}
		}
	}

	/**
	 * 构造数据并插入到对应的表
	 *
	 * @param string $table
	 * @param callable $callback
	 * @param int $num
	 * @param string $locale
	 */
	protected function factoryToTable($table, $callback, $num = 0, $locale = 'zh_CN')
	{
		$table = $this->table($table);
		$this->factory(function ($faker) use (&$table, &$callback) {
			$data = call_user_func($callback, $faker);
			if ($data === false) return false;

			if (is_array($data)) {
				$table->insert($data)->save();
			}

			return true;
		}, $num, $locale);
	}

	/**
	 * Faker
	 *
	 * @param string $locale
	 * @return \Faker\Generator
	 */
	protected function faker($locale = 'zh_CN')
	{
		return \Faker\Factory::create($locale);
	}

}
