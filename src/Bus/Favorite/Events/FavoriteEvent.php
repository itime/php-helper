<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Favorite\Events;

use Xin\Thinkphp\Foundation\Model\Morph;

class FavoriteEvent
{

	/**
	 * @var string
	 */
	protected $topicType;

	/**
	 * @var int
	 */
	protected $topicId;

	/**
	 * @var bool
	 */
	protected $isFavorite;

	/**
	 * @param string $topicType
	 * @param int $topicId
	 */
	public function __construct($topicType, $topicId, $isFavorite)
	{
		$this->topicType = $topicType;
		$this->topicId = $topicId;
		$this->isFavorite = $isFavorite;
	}

	/**
	 * @return string
	 */
	public function getTopicType()
	{
		return $this->topicType;
	}

	/**
	 * @return int
	 */
	public function getTopicId()
	{
		return $this->topicId;
	}

	/**
	 * @return bool
	 */
	public function isFavorite()
	{
		return $this->isFavorite;
	}

	/**
	 * @return string
	 */
	public function getTopicClass()
	{
		return Morph::getType($this->topicType);
	}

}
