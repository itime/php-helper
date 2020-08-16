<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Favorite;

/**
 * Interface Repository
 */
interface Repository{
	
	/**
	 * 根据用户和数据类型获取收藏数据
	 *
	 * @param int $userId
	 * @param int $type
	 * @param int $limit
	 * @return mixed
	 */
	public function getByUserAndType($userId, $type, $limit = null);
	
	/**
	 * 判断主题是否被收藏
	 *
	 * @param int $userId
	 * @param int $favoriteId
	 * @param int $favoriteType
	 * @return mixed
	 */
	public function isFavorite($userId, $favoriteId, $favoriteType);
	
	/**
	 * 立即收藏
	 *
	 * @param int $userId
	 * @param int $favoriteId
	 * @param int $favoriteType
	 * @return mixed
	 */
	public function favorite($userId, $favoriteId, $favoriteType);
	
	/**
	 * 删除收藏
	 *
	 * @param int   $userId
	 * @param array $ids
	 * @return mixed
	 */
	public function delFavorites($userId, array $ids);
}
