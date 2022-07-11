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
	 * @var mixed
	 */
	protected $target;

	/**
	 * @param string $topicType
	 * @param int $topicId
	 * @param bool $isFavorite
	 * @param mixed $target
	 */
	public function __construct($topicType, $topicId, $isFavorite, $target = null)
	{
		$this->topicType = $topicType;
		$this->topicId = $topicId;
		$this->isFavorite = $isFavorite;
		$this->target = $target;
	}

	/**
	 * @return mixed
	 */
	public function getTarget()
	{
		return $this->target;
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
