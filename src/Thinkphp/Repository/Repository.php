<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Repository;

use think\db\Query;
use think\exception\ValidateException;
use think\facade\Db;
use think\Model;
use think\model\Collection;
use think\Paginator;
use think\Validate;
use Xin\Repository\AbstractRepository;
use Xin\Support\Arr;

class Repository extends AbstractRepository
{
	/**
	 * 注册搜索中间件
	 * @return $this
	 */
	public function useSearchMiddleware()
	{
		$this->filterMiddleware(function ($input, $next) {
			if (!isset($input['options']['search'])) {
				$input['options']['search'] = [];
			}

			$search = $input['options']['search'];
			if (empty($search)) {
				return $next($input);
			}

			/** @var Query $query */
			$query = $input['query'];
			$model = $query->getModel();
			if (method_exists($model, 'scopeSearch')) {
				/** @noinspection VariableFunctionsUsageInspection */
				call_user_func([$query, 'search'], $search);
			} else {
				$fields = $model && method_exists($model, 'getSearchFields')
					? $model->getSearchFields()
					: $this->getSearchFields();

				$search = array_filter($search, 'filled');
				$query->withSearch($fields, $search);
			}

			return $next($input);
		});

		return $this;
	}

	/**
	 * @inerhitDoc
	 * @return \think\model\Collection
	 */
	public function lists($search = [], array $with = [], array $options = [])
	{
		$options['search'] = $search;
		$options['paginate'] = null;

		return $this->filter(null, $with, $options);
	}

	/**
	 * @inerhitDoc
	 * @return \think\Paginator
	 */
	public function paginate($search = [], array $with = [], $paginate = 1, array $options = [])
	{
		$options['search'] = $search;
		$options['paginate'] = is_array($paginate) ? $paginate : [
			'page' => $paginate,
		];

		return $this->filter(null, $with, $options);
	}

	/**
	 * @inerhitDoc
	 * @return Paginator|Collection
	 */
	public function filter($filter = null, array $with = [], array $options = [])
	{
		$options = array_replace_recursive($this->options, $options);
		$query = $this->query($filter, $with, $options);

		return $this->middleware([
			'type' => static::SCENE_FILTER,
			'filter' => $filter,
			'with' => $with,
			'query' => $query,
			'options' => $options,
		], function ($input) use ($query) {
			$options = $input['options'] ?? [];

			$paginate = $options['paginate'] ?? null;
			if ($paginate) {
				$paginate = is_array($paginate) ? $paginate
					: (is_numeric($paginate) ? ['page' => $paginate,] : []);

				$data = $query->paginate($paginate);
			} else {
				$data = $query->select();
			}

			return $data;
		}, static::SCENE_FILTER);
	}

	/**
	 * @inerhitDoc
	 * @return mixed|Model
	 */
	public function detail($filter, array $with = [], array $options = [])
	{
		$options = array_replace_recursive($this->options, $options);
		$query = $this->query($filter, $with, $options);

		return $this->middleware([
			'type' => static::SCENE_DETAIL,
			'filter' => $filter,
			'with' => $with,
			'query' => $query,
			'options' => $options,
		], function () use ($query, $options) {
			if ($options['find_or_fail'] ?? false) {
				return $query->findOrFail();
			}

			return $query->find();
		}, static::SCENE_DETAIL);
	}

	/**
	 * @inerhitDoc
	 * @return mixed|Model
	 */
	public function detailById($id, array $with = [], $options = [])
	{
		return $this->detail(['id' => $id], $with, $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function validate(array $data, $scene = null, array $options = [])
	{
		return $this->middleware([
			'type' => static::SCENE_VALIDATE,
			'scene' => $scene,
			'data' => $data,
			'options' => $options,
		], function ($input) use ($scene) {
			$data = $input['data'] ?? [];

			$validator = $this->getOption('validator');
			if ($validator) {
				if ($validator instanceof Validate) {
					$validate = $validator;
				} elseif (is_array($validator)) {
					$validate = new Validate();
					$validate->rule(
						$validator['rules'] ?? [],
						$validator['fields'] ?? []
					)->message($validator['messages'] ?? []);
				} else {
					/** @var \think\Validate $validate */
					$validate = app($validator);
				}

				if ($scene) {
					$validate->scene($scene);
				}

				$validate->failException(true)->check($data);
			}

			return $data;
		}, static::SCENE_VALIDATE);
	}

	/**
	 * @inerhitDoc
	 */
	public function store(array $data, array $options = [])
	{
		$options = array_replace_recursive($this->options, $options);
		$data = $this->validate($data, static::SCENE_STORE, $options);

		return $this->transaction(function () use ($data, $options) {
			return $this->middleware([
				'type' => static::SCENE_STORE,
				'data' => $data,
				'options' => $options,
			], function ($input) {
				$data = $input['data'] ?? [];

				$model = $this->fill($data);
				$model->save();

				return $model;
			}, static::SCENE_STORE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function update($filter, array $data, array $options = [])
	{
		$options = array_replace_recursive($this->options, $options);
		$data = $this->validate($data, static::SCENE_UPDATE);

		return $this->transaction(function () use ($filter, $data, $options) {
			$query = $this->query($filter, [], $options);

			return $this->middleware([
				'type' => static::SCENE_UPDATE,
				'filter' => $filter,
				'data' => $data,
				'query' => $query,
				'options' => $options,
			], function ($input) use ($query) {
				$data = $input['data'] ?? [];

				$model = $query->findOrFail();
				$this->fill($data, $model)->save();

				return $model;
			}, static::SCENE_UPDATE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function updateById($id, array $data, array $options = [])
	{
		return $this->update([
			'id' => $id,
		], $data, $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function setValue(array $ids, $field, $value, array $options = [])
	{
		$options = array_replace_recursive($this->options, $options);
		if (!$this->isAllowSetField($field)) {
			throw new ValidateException("{$field} not in allow field list.");
		}

		// 验证规则
		$allowSetFields = $this->getAllowSetFields();
		if (isset($allowSetFields[$field]) && ($validateRule = $allowSetFields[$field])) {
			(new Validate)->failException(true)->checkRule($value, $validateRule);
		}

		return $this->transaction(function () use ($ids, $field, $value, $options) {
			$query = $this->query([
				['id', 'IN', $ids]
			], [], $options);

			return $this->middleware([
				'type' => static::SCENE_SET_VALUE,
				'ids' => $ids,
				'field' => $field,
				'value' => $value,
				'query' => $query,
				'options' => $options,
			], function ($input) {
				/** @var Query $query */
				$query = $input['query'];
				$query->update([
					$input['field'] => $input['value'],
				]);
				return $input['value'];
			}, static::SCENE_SET_VALUE);
		});
	}

	/**
	 * 是否允许设置字段
	 *
	 * @param string $field
	 * @return bool
	 */
	protected function isAllowSetField($field)
	{
		$allowSetFields = $this->getAllowSetFields();

		return in_array($field, array_map('strval', array_keys($allowSetFields)), true);
	}

	/**
	 * 获取允许修改字段
	 *
	 * @return array
	 */
	protected function getAllowSetFields()
	{
		return array_merge([
			'status' => 'in:0,1',
		], $this->getOption('allow_fields', []));
	}

	/**
	 * @inerhitDoc
	 */
	public function delete($filter, array $options = [])
	{
		$options = array_replace_recursive($this->options, $options);
		return $this->transaction(function () use ($filter, $options) {
			$query = $this->query($filter, [], $options);

			return $this->middleware([
				'type' => static::SCENE_DELETE,
				'filter' => $filter,
				'query' => $query,
				'options' => $options,
			], function ($input) use ($query) {
				$isForce = $input['options']['allow_force_delete'] ?? false;
				if ($isForce) {
					return $query->removeOption('soft_delete')->delete(true);
				}

				return $query->delete();
			}, static::SCENE_DELETE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function deleteByIdList(array $ids, array $options = [])
	{
		return $this->delete([
			['id', 'in', $ids],
		], $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function restore($filter, array $options = [])
	{
		$options = array_replace_recursive($this->options, $options);
		return $this->transaction(function () use ($filter, $options) {
			$query = $this->query($filter, [], $options)->withTrashed();

			return $this->middleware([
				'type' => static::SCENE_RESTORE,
				'filter' => $filter,
				'query' => $query,
				'options' => $options,
			], function () use ($query) {
				return $query->restore();
			}, static::SCENE_RESTORE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function restoreByIdList(array $ids, array $options = [])
	{
		return $this->restore([
			['id', 'in', $ids],
		], $options);
	}

	/**
	 * @inerhitDoc
	 */
	protected function transaction(callable $callback)
	{
		return Db::transaction($callback);
	}

	/**
	 * @return Model|Db
	 */
	protected function modelOrDbQuery()
	{
		if (isset($this->options['model'])) {
			$modelClass = $this->options['model'];
			/** @var \think\Model $model */
			return new $modelClass();
		}

		if (isset($this->options['table'])) {
			$table = $this->options['table'];
			return Db::table($table);
		}

		throw new \RuntimeException('Not support query type.');
	}

	/**
	 * 填入数据
	 * @param array $data
	 * @return Db|Model
	 */
	protected function fill($data = [], $query = null)
	{
		$query = $query ?: $this->modelOrDbQuery();
		if ($query instanceof Model) {
			$query->data($data, true);
		} else {
			$fields = $query->getTableFields();
			$data = Arr::only($data, $fields);
			$query->data($data);
		}

		return $query;
	}

	/**
	 * @inerhitDoc
	 */
	public function query($filter, array $with = [], array $options = [])
	{
		$query = $this->modelOrDbQuery();
		if ($query instanceof Model) {
			$query = $query->db();
		}

		$query->with($with);

		if ($filter) {
			$query->where($filter);
		}

		$allowSetOptions = ['select', 'order'];
		foreach ($options as $key => $option) {
			if (!in_array($key, $allowSetOptions, true)) {
				continue;
			}

			$query->{$key}($option);
		}

		return $query;
	}

	/**
	 * @inerhitDoc
	 */
	public function import($list, array $options = [])
	{
		// TODO: Implement import() method.
	}

	/**
	 * @inerhitDoc
	 */
	public function export($path, array $options = [])
	{
		// TODO: Implement export() method.
	}
}
