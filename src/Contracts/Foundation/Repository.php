<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

interface Repository{

	/**
	 * 获取数据列表
	 *
	 * @param array|callable $where
	 * @param array          $with
	 * @param array          $options
	 * @return mixed
	 */
	public function lists($where = [], array $with = [], array $options = []);

	/**
	 * 获取数据分页
	 *
	 * @param array|callable $where
	 * @param array          $with
	 * @param array          $options
	 * @return mixed
	 */
	public function paginate($where = [], array $with = [], array $options = []);

	/**
	 * 数据过滤
	 *
	 * @param array|callable $where
	 * @param array          $with
	 * @param array          $options
	 * @return mixed
	 */
	public function filter($where = [], array $with = [], array $options = []);

	/**
	 * 获取一条数据
	 *
	 * @param array|callable $where
	 * @param array          $with
	 * @param array          $options
	 * @return mixed
	 */
	public function get($where = [], $with = [], array $options = []);

	/**
	 * 获取一条数据
	 *
	 * @param int   $id
	 * @param array $with
	 * @param array $options
	 * @return mixed
	 */
	public function getById($id, $with = [], array $options = []);

	/**
	 * 获取数据详情
	 *
	 * @param array|callable $where
	 * @param array          $with
	 * @param array          $options
	 * @return mixed
	 */
	public function info($where = [], $with = [], array $options = []);

	/**
	 * 获取数据详情
	 *
	 * @param int   $id
	 * @param array $options
	 * @return mixed
	 */
	public function infoById($id, array $with = [], $options = []);

	/**
	 * 创建数据
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function create(array $data);

	/**
	 * 更新数据
	 *
	 * @param array|callable $where
	 * @param array          $data
	 * @return mixed
	 */
	public function update($where, array $data);

	/**
	 * 根据id更新数据
	 *
	 * @param int   $id
	 * @param array $data
	 * @return mixed
	 */
	public function updateById($id, array $data);

	/**
	 * 验证数据合法性
	 *
	 * @param array  $data
	 * @param string $scene
	 * @return array
	 */
	public function validate(array $data, $scene = 'create');

	/**
	 * 设置数据字段值
	 *
	 * @param        $ids
	 * @param string $field
	 * @param mixed  $value
	 * @return array
	 */
	public function setField($ids, $field, $value);

	/**
	 * 删除数据
	 *
	 * @param array|string|int $where
	 */
	public function delete($where, $force = false);

	/**
	 * 根据ID列表删除数据
	 *
	 * @param array|string|int $ids
	 */
	public function deleteByIdList($ids, $force = false);

	/**
	 * 恢复数据
	 *
	 * @param array $where
	 * @return int
	 */
	public function restore($where);

	/**
	 * 根据id恢复数据
	 *
	 * @param array $ids
	 * @return int
	 */
	public function restoreByIdList($ids);

	/**
	 * 导入
	 *
	 * @param \iterable $list
	 * @return mixed
	 */
	public function import($list);

	/**
	 * 导出
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function export($path);
}
