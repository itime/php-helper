<?php

namespace Xin\Wechat\EasyWechat\OfficialAccount;

use EasyWeChat\OfficialAccount\Application as BaseApplication;

class Application extends BaseApplication {


	public function __construct(array $config = [], array $prepends = [], string $id = null) {

		parent::__construct($config, $prepends, $id);
	}

}
