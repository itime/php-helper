<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Controller;

use think\Model;

/**
 * @method string fetch(string $template = null, array $data = [])
 * @method void assign(string $name, mixed $data = [])
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
	protected function renderIndexResponse($data)
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
	protected function renderDetailResponse($info)
	{
		return $this->fetch(
			$this->property('detailTpl', 'detail'),
			[
				'info' => $info,
			]
		);
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

			return $this->showCreateForm($info);
		}

		return $this->CURDCreate();
	}

	/**
	 * 渲染数据创建页面
	 * @param Model $info
	 * @return string
	 */
	protected function showCreateForm($info)
	{
		return $this->fetch(
			$this->property('editTpl', 'edit'),
			[
				'info' => $info,
			]
		);
	}

	/**
	 * @inerhitDoc
	 */
	public function update()
	{
		if ($this->request->isGet()) {
			$id = $this->request->validId();
			$info = $this->attachHandler('detailable')
				->repository()
				->detailById($id);

			return $this->showUpdateForm($info);
		}

		return $this->CURDUpdate();
	}

	/**
	 * 渲染数据更新页面
	 * @param Model $info
	 * @return string
	 */
	protected function showUpdateForm($info)
	{
		return $this->fetch(
			$this->property('editTpl', 'edit'),
			[
				'info' => $info,
			]
		);
	}

	/**
	 * 跳转地址
	 *
	 * @param string $fallback
	 * @return string
	 */
	protected function jumpUrl($fallback = 'index')
	{
		return $this->request->previousUrl($fallback);
	}
}
