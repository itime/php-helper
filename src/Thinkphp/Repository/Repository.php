<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Repository;

use think\db\Query;
use think\facade\Db;
use think\Model;
use think\model\Collection;
use think\Paginator;
use Xin\Repository\AbstractRepository;

class Repository extends AbstractRepository
{
	/**
	 * 注册搜索中间件
	 * @return void
	 */
	protected function registerSearchMiddleware()
	{
		$this->filterable(function ($input, $next) {
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
		$query = $this->query($filter, $with, $options);

		return $this->middleware([
			'type' => static::SCENE_DETAIL,
			'filter' => $filter,
			'with' => $with,
			'query' => $query,
			'options' => $options,
		], function () use ($query, $options) {
			if ($options['fail'] ?? false) {
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
	 * @return mixed|Model
	 */
	public function show($filter, array $with = [], array $options = [])
	{
		$query = $this->query($filter, $with, $options);

		return $this->middleware([
			'type' => static::SCENE_SHOW,
			'filter' => $filter,
			'with' => $with,
			'query' => $query,
			'options' => $options,
		], function () use ($query, $options) {
			if ($options['fail'] ?? false) {
				return $query->findOrFail();
			}

			return $query->find();
		}, static::SCENE_SHOW);
	}

	/**
	 * @inerhitDoc
	 * @return mixed|Model
	 */
	public function showById($id, array $with = [], array $options = [])
	{
		return $this->show(['id' => $id], $with, $options);
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
		], function ($input) {
			return $input['data'] ?? [];
		}, static::SCENE_VALIDATE);
	}

	/**
	 * @inerhitDoc
	 */
	public function store(array $data, array $options = [])
	{
		$data = $this->validate($data, static::SCENE_STORE, $options);

		return $this->transaction(function () use ($data, $options) {
			$query = $this->query(null, [], $options);

			return $this->middleware([
				'type' => static::SCENE_STORE,
				'data' => $data,
				'query' => $query,
				'options' => $options,
			], function ($input) use ($query) {
				$query->save($input['data'] ?? []);

				return $query;
			}, static::SCENE_STORE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function update($filter, array $data, array $options = [])
	{
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
				$model = $query->findOrFail();

				$model->save($input['data'] ?? []);

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
	public function delete($filter, array $options = [])
	{
		return $this->transaction(function () use ($filter, $options) {
			$query = $this->query($filter, [], $options);

			return $this->middleware([
				'type' => static::SCENE_DELETE,
				'filter' => $filter,
				'query' => $query,
				'options' => $options,
			], function () use ($query) {
				return $query->delete(true);
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
	public function recovery($filter, array $options = [])
	{
		return $this->transaction(function () use ($filter, $options) {
			$query = $this->query($filter, [], $options);

			$input = [
				'type' => static::SCENE_RECOVERY,
				'filter' => $filter,
				'query' => $query,
				'options' => $options,
			];

			return $this->middleware($input, function () use ($query) {
				return $query->delete();
			}, static::SCENE_RECOVERY);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function recoveryByIdList(array $ids, array $options = [])
	{
		return $this->recovery([
			['id', 'in', $ids],
		], $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function restore($filter, array $options = [])
	{
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
	 * @inerhitDoc
	 */
	public function query($filter, array $with = [], array $options = [])
	{
		if (isset($this->options['model'])) {
			$modelClass = $this->options['model'];
			/** @var \think\Model $model */
			$model = new $modelClass();

			/** @var Query $query */
			$query = $model->db();
		} elseif (isset($this->options['table'])) {
			$table = $this->options['table'];
			$query = Db::table($table);
		} else {
			throw new \RuntimeException('Not support query type.');
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
	public function setField(array $ids, $field, $value, array $options = [])
	{
		// TODO: Implement setField() method.
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
