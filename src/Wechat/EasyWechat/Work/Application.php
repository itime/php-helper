<?php

namespace Xin\Wechat\EasyWechat\Work;

use EasyWeChat\Work\Application as BaseApplication;
use Xin\Wechat\EasyWechat\Work\Corp\ServiceProvider as CorpServiceProvider;
use Xin\Wechat\EasyWechat\Work\ExternalContact\ServiceProvider as ExternalContactServiceProvider;
use Xin\Wechat\EasyWechat\Work\User\ServiceProvider as UserServiceProvider;

/**
 * Application.
 * @property  \Xin\Wechat\EasyWechat\Work\User\Client $user
 * @property \Xin\Wechat\EasyWechat\Work\Corp\Client  $corp
 */
class Application extends BaseApplication {

	public function __construct(array $config = [], array $prepends = [], string $id = null) {
		$this->providers[] = CorpServiceProvider::class;
		$this->providers[] = ExternalContactServiceProvider::class;
		$this->providers[] = UserServiceProvider::class;

		parent::__construct($config, $prepends, $id);
	}

}
