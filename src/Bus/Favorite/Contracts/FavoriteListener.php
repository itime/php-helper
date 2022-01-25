<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Favorite\Contracts;

use app\common\event\FavoriteEvent;

interface FavoriteListener {

	/**
	 * 收藏/取消收藏回调
	 * @param FavoriteEvent $event
	 * @return mixed
	 */
	public static function onFavorite(FavoriteEvent $event);

}
