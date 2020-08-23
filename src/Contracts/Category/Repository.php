<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Category;

use Xin\Contracts\Foundation\Repository as BaseRepository;

/**
 * Interface Category.
 */
interface Repository extends BaseRepository{
	
	/**
	 * 根据id获取子分类ID列表
	 *
	 * @param      $categoryId
	 * @param bool $excludeSelf
	 * @return mixed
	 */
	
	public function getSubIdsById($categoryId, $excludeSelf = false);
	
	/**
	 * 获取分类列表
	 *
	 * @param int  $depth
	 * @param bool $isTree
	 * @return mixed
	 */
	public function getCategories($depth = 0, $isTree = false);
	
	/**
	 * 获取子分类数据
	 *
	 * @param mixed $catKeyword
	 * @param int   $depth
	 * @return mixed
	 */
	public function getSubCategoriesByNameOrId($catKeyword, $depth = 0);
	
}
