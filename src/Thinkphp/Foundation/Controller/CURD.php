<?php

namespace Xin\Thinkphp\Foundation\Controller;

use Closure;
use Xin\Contracts\Foundation\Repository;
use Xin\Thinkphp\Facade\Hint;
use Xin\Thinkphp\Http\HasValidate;

/**
 * @property HasValidate $request
 */
trait CURD {

	/**
	 * 列表
	 * @return \think\Response
	 */
	public function lists() {
		$this->attachHandler('filterable');

		$data = $this->repository()->paginate();

		return Hint::result($data);
	}

	/**
	 * 详情
	 * @return \think\Response
	 */
	public function detail() {
		$id = $this->request->idWithValid();

		$this->attachHandler('detailable');

		$info = $this->repository()->detail($id);

		return Hint::result($info);
	}

	/**
	 * 创建数据
	 * @return \think\Response
	 */
	public function store() {
		$this->attachHandler('validateable');

		$this->attachHandler('storeable');

		$info = $this->repository()->store(
			$this->request->param()
		);

		return Hint::success('创建成功！', $info);
	}

	/**
	 * 显示数据
	 * @return \think\Response
	 */
	public function show() {
		$id = $this->request->idWithValid();

		$this->attachHandler('showable');

		$info = $this->repository()->show($id);

		return Hint::result($info);
	}

	/**
	 * 更新数据
	 * @return \think\Response
	 */
	public function update() {
		$id = $this->request->idWithValid();

		$this->attachHandler('validateable');
		$this->attachHandler('updateable');

		$info = $this->repository()->update($id, $request->input());

		return Hint::success('保存成功！', $info);
	}

	/**
	 * 删除数据
	 * @return \think\Response
	 */
	public function delete() {
		$ids = $this->request->idsWithValid();

		$this->attachHandler('deleteable');

		$result = $this->repository()->delete($ids, true);

		return Hint::success('删除成功！', $result);
	}

	/**
	 * 回收数据
	 * @return \think\Response
	 */
	public function recovery() {
		$ids = $this->request->idsWithValid();

		$this->attachHandler('recoveryable');

		$result = $this->repository()->delete($ids, false);

		return Hint::success('删除成功！', $result);
	}

	/**
	 * 恢复数据
	 * @return \think\Response
	 */
	public function restore() {
		$ids = $this->request->idsWithValid();

		$this->attachHandler('restoreable');

		$result = $this->repository()->restore($ids);

		return Hint::success('恢复成功！', $result);
	}

	/**
	 * @param string $name
	 * @return void
	 */
	protected function attachHandler($name) {
		if (method_exists($this, $name)) {
			$this->repository()->$name(Closure::fromCallable([$this, $name]));
		}
	}

	/**
	 * @return Repository
	 */
	abstract protected function repository();

}
