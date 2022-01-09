<?php

namespace Xin\Bot;

use Xin\Capsule\Service;
use Xin\Contracts\Bot\Bot;
use Xin\Http\HasHttpRequests;
use Xin\Support\Arr;

class DingTalk extends Service implements Bot {

	use HasHttpRequests;

	const BASE_URL = 'https://oapi.dingtalk.com/robot/send';

	/**
	 * @inheritDoc
	 */
	public function sendMessage(array $message, array $mentionedList = null) {
		if ($mentionedList) {
			$mentionedOpts = [];
			if (($isAllIndex = array_search('@all', $mentionedList)) !== false) {
				$mentionedOpts['isAtAll'] = true;
				array_splice($mentionedList, $isAllIndex, 1);
			}

			$mentionedOpts['atUserIds'] = $mentionedList;

			$message['at'] = $mentionedOpts;
		}

		$response = $this->httpPostJson(self::BASE_URL, $message, $this->newQueryData());

		if (!$response->ok() || $response->json('errcode') !== 0) {
			return false;
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function sendTextMessage(string $content, array $mentionedList = null): bool {
		return $this->sendMessage([
			'msgtype' => 'text',
			'text' => [
				'content' => $content,
			],
		], $mentionedList);
	}

	/**
	 * @inheritDoc
	 */
	public function sendMarkdownMessage($content, array $mentionedList = null) {
		return $this->sendMessage([
			'msgtype' => 'markdown',
			'markdown' => [
				'title' => '>>>',
				'text' => $content,
			],
		], $mentionedList);
	}

	/**
	 * @inheritDoc
	 */
	public function sendFeedCardMessage($articles, array $mentionedList = null) {
		return $this->sendMessage([
			'msgtype' => 'feedCard',
			'feedCard' => [
				'links' => array_map(function ($item) {
					return Arr::transformKeys($item, [
						'url' => 'messageURL',
						'picurl' => 'picURL',
					]);
				}, $articles),
			],
		], $mentionedList);
	}

	/**
	 * 生成query请求参数
	 * @return array
	 */
	protected function newQueryData() {
		$query = [
			'access_token' => $this->config['key'],
		];

		if (isset($this->config['secret']) && !empty($this->config['secret'])) {
			$timestamp = now()->getTimestamp();
			$query['timestamp'] = $timestamp * 1000;
			$query['sign'] = $this->sign($timestamp);
		}

		return $query;
	}

	/**
	 * @param int $timestamp
	 * @return string
	 */
	protected function sign($timestamp) {
		$stringToSign = ($timestamp * 1000) . "\n" . $this->config['secret'];
		$signData = hash_hmac('sha256', $stringToSign, $this->config['secret'], true);

		return urlencode(base64_encode($signData));
	}

}
