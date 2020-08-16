<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @author 晋<657306123@qq.com>
 * @date 2019/2/20 14:59
 */

namespace Xin\Thinkphp\Controller;

use think\exception\HttpException;
use think\exception\ValidateException;
use think\facade\View;
use Xin\Support\Str;

/**
 * Trait CURD
 *
 * @property-read \Xin\Thinkphp\Http\RequestOptimize $request
 * @property-read string                             $editTpl
 * @property-read string                             $createTpl
 * @property-read string                             $updateTpl
 * @property-read mixed                              $validator
 * @property-read mixed                              $model
 * @property-read array                              $statusCondition
 * @mixin \think\Controller
 */
trait CURD{

	/**
	 * 验证数据合法性
	 *
	 * @param array  $data
	 * @param string $scene
	 */
	private function tryValidate($data, $scene){
		$validator = $this->resolveValidator($scene);
		if(!$validator){
			return;
		}

		if(!$validator->check($data)){
			throw new ValidateException($validator->getError());
		}
	}

	/**
	 * 显示创建视图
	 *
	 * @return string
	 */
	protected function showCreateView(){
		return View::fetch($this->resolveProperty('createTpl', 'edit'));
	}

	/**
	 * 数据创建之前回调
	 *
	 * @param \think\Model $model
	 * @param array        $data
	 * @return array
	 */
	protected function beforeCreate($model, $data){
		return $data;
	}

	/**
	 * 添加行为
	 *
	 * @return mixed
	 */
	public function create(){
		if($this->request->isPost()){
			$data = $this->request->param();
			$this->tryValidate($data, 'add');

			$model = $this->resolveModel();
			$data = $this->beforeCreate($model, $data);
			if($model->allowField(true)->save($data) === false){
				$this->error($model->getError() ? $model->getError() : "添加失败！");
			}
			$this->afterCreate($model, $data);

			$this->success("添加成功！", $this->request->param("http_referer", 'index'));
		}

		return $this->showCreateView();
	}

	/**
	 * 数据创建之后回调
	 *
	 * @param \think\Model $model
	 * @param array        $data
	 */
	protected function afterCreate($model, $data){
	}

	/**
	 * 显示查看视图
	 *
	 * @param \think\Model $model
	 * @return string
	 */
	protected function showEditView($model){
		$this->assign('info', $model);
		return View::fetch($this->resolveProperty('editTpl', 'edit'));
	}

	/**
	 * 编辑行为项
	 *
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function edit(){
		$model = $this->findIsEmptyAssert();
		return $this->showEditView($model);
	}

	/**
	 * 显示更新视图
	 *
	 * @param \think\Model $model
	 * @return string
	 */
	protected function showUpdateView($model){
		$this->assign('info', $model);
		return View::fetch($this->resolveProperty('updateTpl', 'edit'));
	}

	/**
	 * 数据更新之前回调
	 *
	 * @param \think\Model $model
	 * @param array        $data
	 * @return array
	 */
	protected function beforeUpdate($model, $data){
		return $data;
	}

	/**
	 * 更新行为
	 *
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function update(){
		$model = $this->findIsEmptyAssert();

		if($this->request->isPost()){
			$data = $this->request->param();
			$this->tryValidate($data, 'update');

			$data = $this->beforeUpdate($model, $data);
			if($model->allowField(true)->save($data) === false){
				$this->error($model->getError() ? $model->getError() : "更新失败！");
			}
			$this->afterUpdate($model, $data);

			$this->success("更新成功！", $this->request->param("http_referer", 'index'));
		}

		return $this->showUpdateView($model);
	}

	/**
	 * 数据更新之后回调
	 *
	 * @param \think\Model $model
	 * @param array        $data
	 */
	protected function afterUpdate($model, $data){
	}

	/**
	 * 数据删除之前操作
	 *
	 * @param array $ids
	 */
	protected function beforeDelete($ids){
	}

	/**
	 * 删除数据
	 *
	 * @throws \think\Exception
	 * @throws \think\exception\PDOException
	 */
	public function delete(){
		$ids = $this->request->idsWithValid();
		$force = $this->request->param('force/d', 0);

		$this->beforeDelete($ids);

		$modelClass = $this->getModelClass();
		$allowForceDelete = $this->resolveProperty('allowForceDelete', false);
		if($allowForceDelete && $force){
			/** @var \think\db\Query $query */
			$query = call_user_func([$modelClass, 'withTrashed']);
			if($query->where('id', 'in', $ids)->delete(true) === false){
				$this->error("删除失败！");
			}
		}else{
			if(call_user_func([$modelClass, 'destroy'], $ids) === false){
				$this->error("删除失败！");
			}
		}

		$this->afterDelete($ids);

		$this->success("删除成功！");
	}

	/**
	 * 数据删除之后操作
	 *
	 * @param array $ids
	 */
	protected function afterDelete($ids){
	}

	/**
	 * 根据id获取数据，如果为空将中断执行
	 *
	 * @param int $id
	 * @return array|string|\think\Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	protected function findIsEmptyAssert($id = null){
		if(is_null($id)){
			$id = $this->request->idWithValid();
		}

		return $this->resolveModel()->findOrFail($id);
	}

	/**
	 * 是否允许设置字段
	 *
	 * @param string $field
	 * @return bool
	 */
	protected function isAllowField(string $field){
		$allowFields = $this->resolveProperty('allowFields', ['status']);
		return in_array($field, $allowFields);
	}

	/**
	 * 设置字段值
	 *
	 * @param string $field
	 * @throws \ReflectionException
	 */
	public function setFieldValue($field){
		if(!$this->isAllowField($field)){
			throw new HttpException(403, "{$field} not in allow field list.");
		}

		$ids = $this->request->idsWithValid();
		$value = $this->request->param("{$field}/d");

		$valueCondition = $this->resolveProperty("{$field}Condition", [0, 1]);
		if(!in_array($value, $valueCondition)){
			$this->error("参数错误[param {$field} invalid]！");
		}

		$studly = Str::studly($field);
		$this->invokeMethod("beforeSet{$studly}", [$ids, $value]);
		if($this->getModelClass()::update([$field => $value], [
				['id', 'IN', $ids],
			]) === false){
			$this->error("更新失败！");
		}
		$this->invokeMethod("afterSet{$studly}", [$ids, $value]);

		$this->success("更新成功！");
	}

	/**
	 * 获取验证器路径
	 *
	 * @param string $scene
	 * @return \think\Validate
	 */
	protected function resolveValidator($scene){
		$validator = $this->resolveProperty(
			'validator', $this->getClassName()
		);

		if(strpos($validator, "\\") === false){
			$validator = "\\app\\common\\validate\\{$validator}Validate";
		}

		if(!class_exists($validator)){
			return null;
		}

		/** @var \think\Validate $validator */
		$validator = $this->app->make($validator);
		$validator->scene($scene);

		return $validator;
	}

	/**
	 * 获取模型实例
	 *
	 * @param array $data
	 * @return \think\Model
	 */
	protected function resolveModel($data = []){
		$class = $this->getModelClass();
		return new $class($data);
	}

	/**
	 * 获取模型类路径
	 *
	 * @return mixed
	 */
	protected function getModelClass(){
		$model = $this->resolveProperty(
			'model', $this->getClassName()
		);

		if(is_string($model)){
			if(strpos($model, "\\") === false){
				$model = "\\app\\common\\model\\{$model}";
			}
		}elseif(is_object($model)){
			$model = get_class($model);
		}

		return $model;
	}

	/**
	 * 解决属性
	 *
	 * @param string $property
	 * @param mixed  $default
	 * @return mixed
	 */
	protected function resolveProperty($property, $default){
		if(property_exists($this, $property)){
			return $this->{$property};
		}

		return $default;
	}

	/**
	 * 获取 class name
	 *
	 * @return string
	 */
	private function getClassName(){
		$class = explode('\\', get_class($this));
		$class = end($class);
		$class = substr($class, 0, strpos($class, "Controller"));
		return $class;
	}

	/**
	 * 调用类方法
	 *
	 * @param       $method
	 * @param array $vars
	 * @throws \ReflectionException
	 */
	private function invokeMethod($method, $vars = []){
		if(!method_exists($this, $method)){
			return;
		}

		$reflect = new \ReflectionMethod($this, $method);
		$reflect->setAccessible(true);
		$this->app->invokeReflectMethod($this, $reflect, $vars);
	}
}
