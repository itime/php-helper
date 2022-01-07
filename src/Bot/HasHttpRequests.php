<?php

namespace Xin\Bot;

trait HasHttpRequests {

	/**
	 * 发送HttpPost请求
	 * @param string $url
	 * @param array  $data
	 * @param array  $options
	 * @return \Illuminate\Http\Client\Response
	 */
	public function httpPostJson(string $url, array $data = [], array $options = []) {
		return Http::post($url, $data);
	}

}
