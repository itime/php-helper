<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Pagination;

use think\paginator\driver\Bootstrap;

class Bootstrap4 extends Bootstrap{

	/**
	 * @var int
	 */
	protected $originalCurrentPage;

	/**
	 * Bootstrap4 constructor.
	 *
	 * @param mixed    $items
	 * @param int      $listRows
	 * @param int      $currentPage
	 * @param int|null $total
	 * @param bool     $simple
	 * @param array    $options
	 */
	public function __construct($items, int $listRows, int $currentPage = 1, int $total = null, bool $simple = false, array $options = []){
		parent::__construct($items, $listRows, $currentPage, $total, $simple, $options);
		$this->currentPage = $currentPage;
		$this->originalCurrentPage = $currentPage;
	}

	/**
	 * 上一页按钮
	 *
	 * @param string $text
	 * @return string
	 */
	protected function getPreviousButton(string $text = "上一页"):string{
		return parent::getPreviousButton($text);
	}

	/**
	 * 下一页按钮
	 *
	 * @param string $text
	 * @return string
	 */
	protected function getNextButton(string $text = '下一页'):string{
		return parent::getNextButton($text);
	}

	/**
	 * 生成一个可点击的按钮
	 *
	 * @param string $url
	 * @param string $page
	 * @return string
	 */
	protected function getAvailablePageWrapper(string $url, string $page):string{
		return '<li class="page-item"><a class="page-link" href="'.htmlentities($url).'">'.$page.'</a></li>';
	}

	/**
	 * 生成一个禁用的按钮
	 *
	 * @param string $text
	 * @return string
	 */
	protected function getDisabledTextWrapper(string $text):string{
		return '<li class="page-item disabled"><a  class="page-link" href="javascript:void(0);">'.$text.'</a></li>';
	}

	/**
	 * 生成一个激活的按钮
	 *
	 * @param string $text
	 * @return string
	 */
	protected function getActivePageWrapper(string $text):string{
		return '<li class="page-item active"><a class="page-link" href="javascript:void(0);">'.$text.'</a></li>';
	}
}
