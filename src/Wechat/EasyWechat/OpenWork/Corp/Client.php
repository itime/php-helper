<?php

namespace Xin\Wechat\EasyWechat\OpenWork\Corp;

use EasyWeChat\OpenWork\Corp\Client as BaseClient;


class Client extends BaseClient {

	/**
	 * 转换为加密的CorpId
	 * @see https://work.weixin.qq.com/api/doc/90001/90143/95327#1.4%20corpid%E8%BD%AC%E6%8D%A2
	 *
	 * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
	 *
	 * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function toOpenCorpId() {
		return $this->httpPostJson('cgi-bin/corp/to_open_corpid', [
			'corpid' => $this->app['config']['corp_id'],
		]);
	}

}
