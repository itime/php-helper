<?php

namespace Xin\Bot;

use Xin\Capsule\Service;
use Xin\Contracts\Bot\Bot;
use Xin\Support\Arr;
use Xin\Support\Traits\HasHttpRequests;

class DingTalk extends Service implements Bot {

	use HasHttpRequests;

	const BASE_URL = 'https://oapi.dingtalk.com/robot/send?access_token=';

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

		$response = $this->httpPostJson($this->resolveUrl(), $message);

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
	 * 解析URL
	 * @return string
	 */
	protected function resolveUrl(): string {
		$key = $this->config['key'];

		return self::BASE_URL . $key;
	}

}
