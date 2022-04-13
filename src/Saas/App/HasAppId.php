<?php

namespace Xin\Saas\App;

trait HasAppId
{
	/**
	 * @var int
	 */
	protected $appId = 0;

	/**
	 * @return int
	 */
	public function getAppId()
	{
		return $this->appId;
	}

	/**
	 * @param int $appId
	 */
	public function setAppId($appId)
	{
		$this->appId = $appId;
	}


}