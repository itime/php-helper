<?php

namespace Xin\Wechat\EasyWechat\OpenWork;

use EasyWeChat\OpenWork\Application as BaseApplication;
use Xin\Wechat\EasyWechat\OpenWork\Corp\ServiceProvider as CorpServiceProvider;


class Application extends BaseApplication
{

	/**
	 * @inheritDoc
	 */
	public function __construct(array $config = [], array $prepends = [], string $id = null)
	{
		$this->providers[] = CorpServiceProvider::class;

		parent::__construct($config, $prepends, $id);
	}

}
