<?php

namespace Xin\Wechat\EasyWechat\Work\User;

use EasyWeChat\Work\User\Client as BaseClient;

class Client extends BaseClient {

	/**
	 * Create a user.
	 *
	 * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
	 *
	 * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function toOpenuserid(array $useridList) {
		return $this->httpPostJson('cgi-bin/batch/userid_to_openuserid', [
			'userid_list' => $useridList,
		]);
	}

}
