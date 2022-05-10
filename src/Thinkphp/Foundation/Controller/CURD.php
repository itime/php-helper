<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Controller;

use think\db\Query;
use think\Model;
use think\model\Collection;
use Xin\Contracts\Repository\Factory as RepositoryFactory;
use Xin\Support\Str;
use Xin\Thinkphp\Facade\Hint;
use Xin\Thinkphp\Http\Requestable;
use Xin\Thinkphp\Repository\Repository;

/**
 * @method void onFilter(Query $query)
 * @method mixed filterable($input, callable $next)
 * @method mixed detailable($input, callable $next)
 * @method void onDetail($info)
 * @method mixed validateable($input, callable $next)
 * @method void onBeforeValidate(&$data, $input)
 * @method void onAfterValidate(&$data, $input)
 * @method mixed storeable($input, callable $next)
 * @method mixed updateable($input, callable $next)
 * @method void onBeforeWrite($data, $input)
 * @method void onAfterWrite(Model $info, $data, $input)
 * @method mixed setvalueable($input, callable $next)
 * @method mixed deleteable($input, callable $next)
 * @method mixed recoveryable($input, callable $next)
 * @method mixed restoreable($input, callable $next)
 * @method array getIndexOptions()
 * @method array getDetailOptions()
 * @property Requestable $request
 * @property array $allowFields
 * @property bool $allowForceDelete
 * @property array $indexWith
 * @property array $indexOptions
 * @property array $detailWith
 * @property array $detailOptions
 */
trait CURD
{
	/**
	 * @var Repository
	 */
	private $repository;

	/**
	 * @return \Closure
	 */
	protected function filterCallback()
	{
		return function ($input, callable $next) {
			if (method_exists($this, 'onFilter')) {
				$this->onFilter($input['query']);
			}

			return $next($input);
		};
	}

	/**
	 * 返回首页数据
	 * @return mixed
	 * @noinspection PhpReturnDocTypeMismatchInspection
	 */
	public function index()
	{
		$search = $this->request->data();

		$with = $this->property('indexWith', []);
		$options = method_exists($this, 'getIndexOptions')
			? $this->getIndexOptions()
			: $this->property('indexOptions', []);
		$options = array_replace_recursive([
			'order' => 'id desc'
		], $options);

		$data = $this->attachHandler('filterable')
			->repository()
			->filterMiddleware($this->filterCallback())
			->useSearchMiddleware()
			->paginate(
				$search,
				$with,
				$this->request->paginate(),
				$options
			);

		return $this->renderIndexResponse($data);
	}

	/**
	 * 渲染首页数据
	 * @param Collection $data
	 * @return \think\Response
	 */
	protected function renderIndexResponse($data)
	{
		return Hint::result($data);
	}

	/**
	 * 返回详情数据
	 * @return mixed
	 * @noinspection PhpReturnDocTypeMismatchInspection
	 */
	public function detail()
	{
		$id = $this->request->validId();

		$with = $this->property('detailWith', []);
		$options = method_exists($this, 'getDetailOptions')
			? $this->getDetailOptions()
			: $this->property('detailOptions', []);

		$info = $this->attachHandler('detailable')
			->repository()
			->detailMiddleware($this->filterCallback())
			->detailById($id, $with, $options);

		if (method_exists($this, 'onDetail')) {
			$this->onDetail($info);
		}

		return $this->renderDetailResponse($info);
	}

	/**
	 * 渲染详情数据
	 * @param Model $info
	 * @return \think\Response
	 */
	protected function renderDetailResponse($info)
	{
		return Hint::result($info);
	}

	/**
	 * 要排除的字段
	 * @return array
	 */
	protected function requestExcludeKeys($scene = null)
	{
		return array_merge(($scene === 'create' ? ['id'] : []), [
			'delete_time', 'create_time', 'update_time'
		]);
	}

	/**
	 * @return \Closure
	 */
	protected function writerCallback()
	{
		return function ($input, callable $next) {
			if (method_exists($this, 'onBeforeWrite')) {
				$this->onBeforeWrite($input['data'], $input);
			}

			$info = $next($input);

			if (method_exists($this, 'onAfterWrite')) {
				$this->onAfterWrite($info, $input['data'], $input);
			}

			return $info;
		};
	}

	/**
	 * @return \Closure
	 */
	protected function validateDataCallback()
	{
		return function ($input, callable $next) {
			if (method_exists($this, 'onBeforeValidate')) {
				$this->onBeforeValidate($input['data'], $input);
			}

			$data = $next($input);

			if (method_exists($this, 'onAfterValidate')) {
				$this->onAfterValidate($data, $input);
			}

			return $data;
		};
	}

	/**
	 * 创建数据操作
	 * @return \think\Response
	 */
	public function create()
	{
		$data = $this->request->data(
			$this->requestExcludeKeys('create')
		);

		$info = $this->attachHandler([
			'validateable', 'storeable'
		])
			->repository()
			->validateMiddleware($this->validateDataCallback())
			->storeMiddleware($this->writerCallback())
			->store($data);

		return $this->renderCreateResponse($info);
	}

	/**
	 * 数据创建成功响应
	 * @param Model $info
	 * @return \think\Response
	 */
	protected function renderCreateResponse($info)
	{
		return Hint::success('创建成功！', $this->jumpUrl(), $info);
	}

	/**
	 * 更新数据操作
	 * @return \think\Response
	 */
	public function update()
	{
		$id = $this->request->validId();

		$data = $this->request->data(
			$this->requestExcludeKeys()
		);

		$info = $this->attachHandler([
			'validateable', 'updateable'
		])
			->repository()
			->validateMiddleware($this->validateDataCallback())
			->updateMiddleware($this->filterCallback())
			->updateMiddleware($this->writerCallback())
			->updateById($id, $data);

		return $this->renderUpdateResponse($info);
	}

	/**
	 * 数据更新成功响应
	 * @param Model $info
	 * @return \think\Response
	 */
	protected function renderUpdateResponse($info)
	{
		return Hint::success('保存成功！', $this->jumpUrl(), $info);
	}

	/**
	 * 设置字段值
	 *
	 * @return \think\Response
	 */
	public function setValue()
	{
		$ids = $this->request->validIds();
		$field = $this->request->validString('field', '', 'trim');
		$value = $this->request->param($field);

		$this->attachHandler('setvalueable')
			->repository()
			->registerMiddleware('setvalueable', $this->filterCallback())
			->setValue($ids, $field, $value);

		return $this->renderSetValueResponse($ids, $field, $value);
	}

	/**
	 * 数据更新成功响应
	 * @param array $ids
	 * @param string $field
	 * @param mixed $value
	 * @return \think\Response
	 */
	protected function renderSetValueResponse($ids, $field, $value)
	{
		return Hint::success('更新成功！');
	}

	/**
	 * 删除/回收数据操作
	 * @return \think\Response
	 */
	public function delete()
	{
		$ids = $this->request->validIds();
		$isForce = $this->request->param('force/d', 0);

		$result = $this->attachHandler('deleteable')
			->repository()
			->deleteMiddleware($this->filterCallback())
			->deleteByIdList($ids, [
				'force' => $isForce
			]);

		return $this->renderDeleteResponse($result);
	}

	/**
	 * 渲染删除响应
	 * @param array $result
	 * @return \think\Response
	 */
	protected function renderDeleteResponse($result)
	{
		return Hint::success('删除成功！', null, $result);
	}

	/**
	 * 恢复数据
	 * @return \think\Response
	 */
	public function restore()
	{
		$ids = $this->request->validIds();

		$result = $this->attachHandler('restoreable')
			->repository()
			->restoreMiddleware($this->filterCallback())
			->restoreByIdList($ids);

		return $this->renderRestoreResponse($result);
	}

	/**
	 * 渲染数据恢复响应
	 * @param array $result
	 * @return \think\Response
	 */
	protected function renderRestoreResponse($result)
	{
		return Hint::success('恢复成功！', null, $result);
	}

	/**
	 * 挂载处理器
	 * @param string|array $scenes
	 * @return $this
	 */
	protected function attachHandler($scenes)
	{
		$scenes = is_array($scenes) ? $scenes : [$scenes];
		$thisRef = new \ReflectionClass($this);

		$repository = $this->repository();
		foreach ($scenes as $scene) {
			if ($thisRef->hasMethod($scene)) {
				$method = $thisRef->getMethod($scene);
				$method->setAccessible(true);
				$handler = $method->getClosure($this);
				if (Str::endsWith($scene, 'able')) {
					$scene = Str::before($scene, 'able');
					$repository->registerMiddleware($scene, $handler);
				} elseif (Str::endsWith($scene, 'middleware')) {
					$repository->$scene($handler);
				} else {
					$repository->registerMiddleware($scene, $handler);
				}
			}
		}

		return $this;
	}

	/**
	 * 获取仓库实例
	 * @return Repository
	 */
	protected function repository(): Repository
	{
		if ($this->repository) {
			return $this->repository;
		}

		$repository = $this->repositoryTo();
		if (!($repository instanceof Repository)) {
			$repository = app(RepositoryFactory::class)->repository($repository);
		}

		if (property_exists($this, 'allowFields')) {
			$repository->setOption('allow_fields', $this->allowFields);
		}

		if (property_exists($this, 'allowForceDelete')) {
			$repository->setOption('allow_force_delete', $this->allowForceDelete);
		}

		return $this->repository = $repository;
	}

	/**
	 * 抽象获取仓库实例
	 * @return string|\Xin\Contracts\Repository\Repository
	 */
	abstract protected function repositoryTo();

	/**
	 * 获取属性
	 *
	 * @param string $property
	 * @param mixed $default
	 * @return mixed
	 */
	protected function property($property, $default = null)
	{
		if (property_exists($this, $property)) {
			return $this->{$property};
		}

		return $default;
	}

	/**
	 * 跳转地址
	 *
	 * @param string $fallback
	 * @return string
	 */
	protected function jumpUrl($fallback = 'index')
	{
		return null;
	}
}
