<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\CURD;

use think\db\Query;
use think\exception\HttpException;
use think\facade\Validate;
use Xin\Support\Arr;
use Xin\Support\Str;
use Xin\Thinkphp\Facade\Hint;

/**
 * @property-read \think\Request|\Xin\Thinkphp\Http\RequestValidate $request
 */
trait InteractsCURD{
	
	use Attribute;
	
	/**
	 * 显示数据列表视图
	 *
	 * @param mixed $data
	 * @return string
	 */
	protected function showListView($data){
		$this->view->assign('data', $data);
		
		return $this->view->fetch($this->property('listTpl', 'index'));
	}
	
	/**
	 * 列表条件处理
	 *
	 * @param \think\db\Query $query
	 * @return \think\db\Query
	 */
	protected function querySelect(Query $query){
		return $query;
	}
	
	/**
	 * 列表处理
	 *
	 * @param mixed $list
	 * @return mixed
	 */
	protected function listHandle($list){
		return $list;
	}
	
	/**
	 * 数据列表
	 *
	 * @return mixed
	 * @throws \think\db\exception\DbException
	 */
	public function index(){
		$keywordField = $this->property('keywordField');
		$keywords = $this->request->keywordsSql();
		
		/** @var Query $query */
		$query = $this->model()
			->when($keywordField && $keywords, [
				[$keywordField, 'like', $keywords],
			])
			->order('id desc');
		
		$tempQuery = call_user_func([$this, 'querySelect'], $query);
		if($tempQuery instanceof Query){
			$query = $tempQuery;
		}elseif(is_array($tempQuery)){
			$query->where($tempQuery);
		}
		
		$data = $query->paginate($this->request->paginate());
		
		$data = $this->listHandle($data);
		
		return $this->showListView($data);
	}
	
	/**
	 * 显示创建视图
	 *
	 * @return string
	 */
	protected function showCreateView(){
		return $this->view->fetch($this->property('createTpl', 'edit'));
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
	 * 数据创建之后回调
	 *
	 * @param \think\Model $model
	 * @param array        $data
	 */
	protected function afterCreate($model, $data){
	}
	
	/**
	 * 添加行为
	 *
	 * @return mixed
	 */
	public function create(){
		if($this->request->isGet()){
			return $this->showCreateView();
		}
		
		$data = $this->request->param();
		unset($data['id']);
		
		$this->validateData($data, 'create');
		
		$model = $this->model();
		
		$data = $this->beforeCreate($model, $data);
		
		$model = $this->writeAllowField($model, $data, false);
		
		if($model->save($data) === false){
			return Hint::error("添加失败！");
		}
		
		$this->afterCreate($model, $data);
		
		return Hint::success("添加成功！", $this->jumpUrl());
	}
	
	/**
	 * 显示查看详情视图
	 *
	 * @param \think\Model $model
	 * @return string
	 */
	protected function showDetailView($model){
		$this->view->assign('info', $model);
		
		return $this->view->fetch($this->property('detailTpl', 'edit'));
	}
	
	/**
	 * 查看详情
	 *
	 * @return mixed
	 */
	public function show(){
		$model = $this->findIsEmptyAssert();
		
		return $this->showDetailView($model);
	}
	
	/**
	 * 显示更新视图
	 *
	 * @param \think\Model $model
	 * @return string
	 */
	protected function showUpdateView($model){
		$this->view->assign('info', $model);
		return $this->view->fetch($this->property('updateTpl', 'edit'));
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
	 * 数据更新之后回调
	 *
	 * @param \think\Model $model
	 * @param array        $data
	 */
	protected function afterUpdate($model, $data){
	}
	
	/**
	 * 更新行为
	 *
	 * @return mixed
	 */
	public function update(){
		$model = $this->findIsEmptyAssert();
		
		if($this->request->isGet()){
			return $this->showUpdateView($model);
		}
		
		$data = $this->request->param();
		$this->validateData($data, 'update');
		
		// 数组转换成模型
		if(is_array($model)){
			$model = $this->model($model);
		}
		
		$data = $this->beforeUpdate($model, $data);
		
		$model = $this->writeAllowField($model, $data, true);
		
		if($model->save($data) === false){
			return Hint::error("更新失败！");
		}
		
		$this->afterUpdate($model, $data);
		
		return Hint::success("更新成功！", $this->jumpUrl());
	}
	
	/**
	 * 写入数据库时允许的字段
	 *
	 * @param \think\db\BaseQuery|\think\Model $model
	 * @param mixed                            $data
	 * @param bool                             $isUpdate
	 * @return mixed
	 */
	protected function writeAllowField($model, &$data, $isUpdate = false){
		if(method_exists($model, 'allowField')){
			return $model->allowField([]);
		}else{
			$fields = $model->getTableFields();
			$data = Arr::only($data, $fields);
			
			return $model;
		}
	}
	
	/**
	 * 获取允许修改字段
	 *
	 * @return array
	 */
	protected function allowFields(){
		return array_merge([
			'status' => 'in:0,1',
		], $this->property('allowFields', []));
	}
	
	/**
	 * 是否允许设置字段
	 *
	 * @param string $field
	 * @return bool
	 */
	protected function isAllowField($field){
		$allowFields = $this->allowFields();
		return in_array($field, array_map('strval', array_keys($allowFields)));
	}
	
	/**
	 * 数据设置之前回调
	 *
	 * @param array  $ids
	 * @param string $field
	 * @param mixed  $value
	 * @return mixed
	 */
	protected function beforeSetField(&$ids, $field, $value){
		return $value;
	}
	
	/**
	 * 数据设置之后回调
	 *
	 * @param array  $ids
	 * @param string $field
	 * @param mixed  $value
	 */
	protected function afterSetField($ids, $field, $value){
	}
	
	/**
	 * 设置字段值
	 *
	 * @param string $field
	 * @return \think\Response
	 * @throws \think\db\exception\DbException
	 */
	public function setFieldValue($field){
		if(!$this->isAllowField($field)){
			throw new HttpException(403, "{$field} not in allow field list.");
		}
		
		$ids = $this->request->idsWithValid();
		$value = $this->request->param("{$field}");
		
		$data = [
			$field => $value,
		];
		
		// 验证规则
		$allowFields = $this->allowFields();
		if(isset($allowFields[$field]) && ($validateRule = $allowFields[$field])){
			$flag = Validate::check($data, [
				$field => $validateRule,
			]);
			
			if(!$flag){
				return Hint::error("参数错误[param {$field} invalid]！");
			}
		}
		
		$this->beforeSetField($ids, $field, $value);
		
		$fieldStudly = Str::studly($field);
		$this->invokeMethod("beforeSet{$fieldStudly}", [&$ids, $field, $value]);
		if($this->model()->update($data, [
				['id', 'IN', $ids],
			]) === false){
			return Hint::error("更新失败！");
		}
		
		$this->afterSetField($ids, $field, $value);
		$this->invokeMethod("afterSet{$fieldStudly}", [$ids, $field, $value]);
		
		return Hint::success("更新成功！", $this->jumpUrl());
	}
	
	/**
	 * 数据删除之前操作
	 *
	 * @param array      $ids
	 * @param array|null $where
	 */
	protected function beforeDelete(&$ids, array &$where = null){
	}
	
	/**
	 * 数据删除之后操作
	 *
	 * @param array $ids
	 */
	protected function afterDelete($ids){
	}
	
	/**
	 * 删除数据
	 *
	 * @return mixed
	 * @throws \think\db\exception\DbException
	 */
	public function delete(){
		$ids = $this->request->idsWithValid();
		$force = $this->request->param('force/d', 0);
		
		$model = $this->model();
		
		$this->beforeDelete($ids, $where);
		if(empty($ids)){
			return Hint::success("删除成功！");
		}
		
		$where = $where ? $where : [];
		$where[] = ['id', 'in', $ids];
		
		$allowForceDelete = $this->property('allowForceDelete', false);
		if($allowForceDelete && $force){
			/** @var \think\db\BaseQuery $query */
			$query = call_user_func([$model, 'withTrashed']);
			if($query->where($where)->delete(true) === false){
				return Hint::error("删除失败！");
			}
		}else{
			if($model instanceof \think\Model){
				$flag = $model::destroy(function(Query $query) use ($where){
					$query->where($where);
				});
			}else{
				$flag = $model->where($where)->delete();
			}
			
			if($flag === false){
				return Hint::error("删除失败！");
			}
		}
		
		$this->afterDelete($ids);
		
		return Hint::success("删除成功！");
	}
	
	/**
	 * 根据id获取数据，如果为空将中断执行
	 *
	 * @param int|null $id
	 * @return array|string|\think\Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function findIsEmptyAssert($id = null){
		if(is_null($id)){
			$id = $this->request->idWithValid();
		}
		
		return $this->model()->findOrFail($id);
	}
	
	/**
	 * 跳转地址
	 *
	 * @param string $fallback
	 * @return mixed
	 */
	protected function jumpUrl($fallback = 'index'){
		return $this->request->previousUrl($fallback);
	}
	
}
