<?php

namespace Xin\Bot;

use Xin\Capsule\Service;
use Xin\Contracts\Bot\Bot;
use Xin\Http\HasHttpRequests;

class QyWork extends Service implements Bot {

	use HasHttpRequests;

	const BASE_URL = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send';

	/**
	 * @inheritDoc
	 */
	public function sendMessage(array $message, array $mentionedList = null): bool {
		if (!empty($mentionedList)) {
			$message['mentioned_list'] = $mentionedList;
		}

		$response = $this->httpPostJson(self::BASE_URL, $message, [
			'key' => $this->config['key'],
		]);

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
				'content' => $content,
			],
		], $mentionedList);
	}

	/**
	 * @inheritDoc
	 */
	public function sendImageMessage($content, $fileMD5, array $mentionedList = null) {
		return $this->sendMessage([
			'msgtype' => 'image',
			'image' => [
				'base64' => $content,
				'md5' => $fileMD5,
			],
		], $mentionedList);
	}

	/**
	 * @inheritDoc
	 */
	public function sendFeedCardMessage($articles, array $mentionedList = null) {
		return $this->sendMessage([
			'msgtype' => 'news',
			'news' => [
				'articles' => $articles,
			],
		], $mentionedList);
	}

	/**
	 * @inheritDoc
	 */
	public function sendFileMessage($mediaId, array $mentionedList = null) {
		return $this->sendMessage([
			'msgtype' => 'file',
			'file' => [
				'media_id' => $mediaId,
			],
		], $mentionedList);
	}

}
