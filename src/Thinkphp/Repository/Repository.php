<?php

namespace Xin\Thinkphp\Repository;

use app\common\model\Model;
use think\db\Query;
use think\facade\Db;
use think\model\Collection;
use think\Paginator;
use Xin\Contracts\Foundation\Repository as RepositoryContract;

class Repository implements RepositoryContract {

	use HasMiddleware;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @param array $options
	 */
	public function __construct(array $options) {
		$this->options = $options;
		$this->middlewareManager = new MiddlewareManager();

		if (isset($options['handler'])) {
			$this->setupHandler($options['handler']);
		}
	}

	/**
	 * @inerhitDoc
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function lists($search = [], array $with = [], array $options = []) {
		$options['paginate'] = false;

		return $this->search($search, $with, $options);
	}

	/**
	 * @inerhitDoc
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
	public function paginate($search = [], array $with = [], $paginate = 1, array $options = []) {
		$options['paginate'] = $paginate;

		return $this->search($search, $with, $options);
	}

	protected function search($search = [], array $with = [], array $options = []) {
		$query = $this->query($with, $options)->withSearch($search);
	}

	/**
	 * @inerhitDoc
	 * @return Paginator|Collection
	 */
	public function filter($filter = null, array $with = [], array $options = []) {
		$query = $this->query($with, $options)->where($filter);

		return $this->middleware([
			'type' => static::SCENE_FILTER,
			'filter' => $filter,
			'with' => $with,
			'query' => $query,
			'options' => $options,
		], function ($input) use ($query) {
			$options = $input['options'] ?? [];

			$paginate = $options['paginate'] ?? false;
			if ($paginate) {
				$paginate = is_array($paginate)
					? $paginate
					: (is_numeric($paginate) ? [
						'per_page' => $paginate,
					] : []);

				$data = $query->paginate(
					$paginate['per_page'] ?? null,
					['*'],
					$paginate['page_name'] ?? 'page',
					$paginate['page'] ?? null
				);
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
	public function detail($filter, array $with = [], array $options = []) {
		$query = $this->query($with, $options)->where($filter);

		return $this->middleware([
			'type' => static::SCENE_DETAIL,
			'filter' => $filter,
			'with' => $with,
			'query' => $query,
			'options' => $options,
		], function ($input) use ($query, $options) {
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
	public function detailById($id, array $with = [], $options = []) {
		return $this->detail(['id' => $id], $with, $options);
	}

	/**
	 * @inerhitDoc
	 * @return mixed|Model
	 */
	public function show($filter, array $with = [], array $options = []) {
		$query = $this->query($with, $options)->where($filter);

		return $this->middleware([
			'type' => static::SCENE_SHOW,
			'filter' => $filter,
			'with' => $with,
			'query' => $query,
			'options' => $options,
		], function ($input) use ($query, $options) {
			if ($options['fail'] ?? false) {
				return $query->firstOrFail();
			}

			return $query->first();
		}, static::SCENE_SHOW);
	}

	/**
	 * @inerhitDoc
	 * @return mixed|Model
	 */
	public function showById($id, array $with = [], array $options = []) {
		return $this->show(['id' => $id], $with, $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function validate(array $data, $scene = null, array $options = []) {
		return $this->middleware([
			'type' => static::SCENE_VALIDATE,
			'scene' => $scene,
			'data' => $data,
			'options' => $options,
		], function ($input) use ($options) {
			return $input['data'] ?? [];
		}, static::SCENE_VALIDATE);
	}

	/**
	 * @inerhitDoc
	 */
	public function store(array $data, array $options = []) {
		$data = $this->validate($data, static::SCENE_STORE, $options);

		return Db::transaction(function () use ($data, $options) {
			$query = $this->query([], $options);

			return $this->middleware([
				'type' => static::SCENE_STORE,
				'data' => $data,
				'query' => $query,
				'options' => $options,
			], function ($input) use ($query, $options) {
				$query->save($input['data'] ?? []);

				return $query;
			}, static::SCENE_STORE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function update($filter, array $data, array $options = []) {
		$data = $this->validate($data, static::SCENE_UPDATE);

		return Db::transaction(function () use ($filter, $data, $options) {
			$query = $this->query([], $options)->where($filter);

			return $this->middleware([
				'type' => static::SCENE_UPDATE,
				'filter' => $filter,
				'data' => $data,
				'query' => $query,
				'options' => $options,
			], function ($input) use ($query, $options) {
				if ($options['fail'] ?? true) {
					$model = $query->findOrFail();
				} else {
					$model = $query->first();
				}

				$model->save($input['data'] ?? []);

				return $model;
			}, static::SCENE_UPDATE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function updateById($id, array $data, array $options = []) {
		return $this->update([
			'id' => $id,
		], $data, $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function delete($filter, array $options = []) {
		return Db::transaction(function () use ($filter, $options) {
			$query = $this->query([], $options)->where($filter);

			return $this->middleware([
				'type' => static::SCENE_DELETE,
				'filter' => $filter,
				'query' => $query,
				'options' => $options,
			], function ($input) use ($query) {
				// todo
				$query->delete(true);
			}, static::SCENE_DELETE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function deleteByIdList(array $ids, array $options = []) {
		return $this->delete([
			['id', 'in', $ids],
		], $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function recovery($filter, array $options = []) {
		return Db::transaction(function () use ($filter, $options) {
			$query = $this->query([], $options)->where($filter);

			$input = [
				'type' => static::SCENE_RECOVERY,
				'filter' => $filter,
				'query' => $query,
				'options' => $options,
			];

			return $this->middleware($input, function ($input) use ($query, $options) {
				return $query->delete();
			}, static::SCENE_RECOVERY);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function recoveryByIdList(array $ids, array $options = []) {
		return $this->recovery([
			['id', 'in', $ids],
		], $options);
	}

	/**
	 * @inerhitDoc
	 */
	public function restore($filter, array $options = []) {
		return Db::transaction(function () use ($filter, $options) {
			$query = $this->query([], $options)->withTrashed()->where($filter);

			return $this->middleware([
				'type' => static::SCENE_RESTORE,
				'filter' => $filter,
				'query' => $query,
				'options' => $options,
			], function ($input) use ($query, $options) {
				return $query->restore();
			}, static::SCENE_RESTORE);
		});
	}

	/**
	 * @inerhitDoc
	 */
	public function restoreByIdList(array $ids, array $options = []) {
		return $this->restore([
			['id', 'in', $ids],
		], $options);
	}

	/**
	 * @param array $with
	 * @param array $options
	 * @return Db|Query
	 */
	public function query(array $with = [], $options = []) {
		if (isset($this->options['model'])) {
			$modelClass = $this->options['model'];
			/** @var Query $query */
			$query = call_user_func([$modelClass, 'query']);
			$query->with($with);
		} elseif (isset($this->options['table'])) {
			$table = $this->options['table'];
			$query = Db::table($table);
		} else {
			throw new \RuntimeException('Not support query type.');
		}

		$allowSetOptions = ['select', 'order'];
		foreach ($options as $key => $option) {
			if (!in_array($key, $allowSetOptions)) {
				continue;
			}

			$query->{$key}($option);
		}

		return $query;
	}

	public function setField(array $ids, $field, $value, array $options = []) {
		// TODO: Implement setField() method.
	}

	public function import($list, array $options = []) {
		// TODO: Implement import() method.
	}

	public function export($path, array $options = []) {
		// TODO: Implement export() method.
	}

}
