<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Controller;

use think\Model;

/**
 * @method string fetch(string $template, array $data)
 * @method void assign(string $name, mixed $data)
 */
trait PageCURD
{
	use CURD {
		CURD::create as CURDCreate;
		CURD::update as CURDUpdate;
	}

	/**
	 * @inerhitDoc
	 */
	public function create()
	{
		if ($this->request->isGet()) {
			$isCopy = $this->request->param('copy', 0);
			$id = $this->request->param('id/d', 0);

			$info = null;
			if ($isCopy && $id > 0) {
				$info = $this->attachHandler('detailable')
					->repository()
					->detailById($id);
			}

			return $this->renderCreate($info);
		}

		return $this->CURDCreate();
	}

	/**
	 * @inerhitDoc
	 */
	public function update()
	{
		if ($this->request->isGet()) {
			$id = $this->request->idWithValid();
			$info = $this->attachHandler('detailable')
				->repository()
				->detailById($id);

			return $this->renderUpdate($info);
		}

		return $this->CURDUpdate();
	}

	/**
	 * @inerhitDoc
	 */
	protected function renderIndex($data)
	{
		return $this->fetch(
			$this->property('listTpl', 'index'),
			[
				'data' => $data,
			]
		);
	}

	/**
	 * @inerhitDoc
	 */
	protected function renderDetail($info)
	{
		return $this->fetch(
			$this->property('detailTpl', 'detail'),
			[
				'info' => $info,
			]
		);
	}

	/**
	 * 渲染数据创建页面
	 * @param Model $info
	 * @return string
	 */
	protected function renderCreate($info)
	{
		return $this->fetch(
			$this->property('editTpl', 'edit'),
			[
				'info' => $info,
			]
		);
	}

	/**
	 * 渲染数据更新页面
	 * @param Model $info
	 * @return string
	 */
	protected function renderUpdate($info)
	{
		return $this->fetch(
			$this->property('editTpl', 'edit'),
			[
				'info' => $info,
			]
		);
	}

	/**
	 * @return mixed
	 * @noinspection PhpReturnDocTypeMismatchInspection
	 */
	public function show()
	{
		$id = $this->request->idWithValid();

		$info = $this->attachHandler('showable')
			->repository()
			->showById($id);

		return $this->renderShow($info);
	}

	/**
	 * @inerhitDoc
	 */
	protected function renderShow($info)
	{
		return $this->fetch(
			$this->property('editTpl', 'show'),
			[
				'info' => $info,
			]
		);
	}
}
