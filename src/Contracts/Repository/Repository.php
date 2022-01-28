<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Repository;

interface Repository
{
	// 过滤数据
	public const SCENE_FILTER = 'filter';

	// 详情数据
	public const SCENE_DETAIL = 'detail';

	// 创建或编辑数据
	public const SCENE_SHOW = 'show';

	// 创建数据
	public const SCENE_STORE = 'store';

	// 更新数据
	public const SCENE_UPDATE = 'update';

	// 删除数据
	public const SCENE_DELETE = 'delete';

	// 回收数据
	public const SCENE_RECOVERY = 'recovery';

	// 恢复数据
	public const SCENE_RESTORE = 'restore';

	// 验证数据
	public const SCENE_VALIDATE = 'validate';

	/**
	 * 获取数据列表
	 *
	 * @param array|callable $search
	 * @param array $with
	 * @param array $options
	 * @return mixed
	 */
	public function lists($search = [], array $with = [], array $options = []);

	/**
	 * 获取数据分页
	 *
	 * @param array|callable $search
	 * @param array $with
	 * @param mixed $paginate
	 * @param array $options
	 * @return mixed
	 */
	public function paginate($search = [], array $with = [], $paginate = 1, array $options = []);

	/**
	 * 数据过滤
	 *
	 * @param array|callable $filter
	 * @param array $with
	 * @param array $options
	 * @return mixed
	 */
	public function filter($filter = [], array $with = [], array $options = []);

	/**
	 * 获取一条数据
	 *
	 * @param array|callable $filter
	 * @param array $with
	 * @param array $options
	 * @return mixed
	 */
	public function show($filter, array $with = [], array $options = []);

	/**
	 * 获取一条数据
	 *
	 * @param int $id
	 * @param array $with
	 * @param array $options
	 * @return mixed
	 */
	public function showById($id, array $with = [], array $options = []);

	/**
	 * 获取数据详情
	 *
	 * @param array|callable $filter
	 * @param array $with
	 * @param array $options
	 * @return mixed
	 */
	public function detail($filter, array $with = [], array $options = []);

	/**
	 * 获取数据详情
	 *
	 * @param int $id
	 * @param array $options
	 * @return mixed
	 */
	public function detailById($id, array $with = [], $options = []);

	/**
	 * 创建数据
	 *
	 * @param array $data
	 * @param array $options
	 * @return mixed
	 */
	public function store(array $data, array $options = []);

	/**
	 * 更新数据
	 *
	 * @param array|callable $filter
	 * @param array $data
	 * @param array $options
	 * @return mixed
	 */
	public function update($filter, array $data, array $options = []);

	/**
	 * 根据id更新数据
	 *
	 * @param int $id
	 * @param array $data
	 * @param array $options
	 * @return mixed
	 */
	public function updateById($id, array $data, array $options = []);

	/**
	 * 验证数据合法性
	 *
	 * @param array $data
	 * @param string $scene
	 * @param array $options
	 * @return array
	 */
	public function validate(array $data, $scene = null, array $options = []);

	/**
	 * 设置数据字段值
	 *
	 * @param array $ids
	 * @param string $field
	 * @param mixed $value
	 * @param array $options
	 * @return array
	 */
	public function setField(array $ids, $field, $value, array $options = []);

	/**
	 * 删除数据
	 *
	 * @param array|string|int $filter
	 * @param array $options
	 * @return mixed
	 */
	public function delete($filter, array $options = []);

	/**
	 * 根据ID列表删除数据
	 *
	 * @param array|string|int $ids
	 * @param array $options
	 * @return int
	 */
	public function deleteByIdList(array $ids, array $options = []);


	/**
	 * 软删除数据
	 *
	 * @param array|string|int $filter
	 * @param array $options
	 * @return mixed
	 */
	public function recovery($filter, array $options = []);

	/**
	 * 根据id软删除数据
	 *
	 * @param array $ids
	 * @param array $options
	 * @return int
	 */
	public function recoveryByIdList(array $ids, array $options = []);

	/**
	 * 恢复数据
	 *
	 * @param array $filter
	 * @param array $options
	 * @return int
	 */
	public function restore($filter, array $options = []);

	/**
	 * 根据id恢复数据
	 *
	 * @param array $ids
	 * @param array $options
	 * @return int
	 */
	public function restoreByIdList(array $ids, array $options = []);

	/**
	 * 导入
	 *
	 * @param \iterable $list
	 * @return mixed
	 */
	public function import($list, array $options = []);

	/**
	 * 导出
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function export($path, array $options = []);

}
