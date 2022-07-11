<?php

namespace Xin\Contracts\Robot;

interface Robot
{

	/**
	 * 发送消息
	 * @param array $message
	 * @param array|null $mentionedList
	 * @return bool
	 */
	public function sendMessage(array $message, array $mentionedList = null);

	/**
	 * 发送文本消息
	 * @param string $content
	 * @param array|null $mentionedList
	 * @param array $attributes
	 * @return bool
	 */
	public function sendTextMessage(string $content, array $mentionedList = null, array $attributes = []);

	/**
	 * 发送Markdown消息
	 * @param string $content
	 * @param array|null $mentionedList
	 * @param array $attributes
	 * @return array|null
	 */
	public function sendMarkdownMessage($content, array $mentionedList = null, array $attributes = []);

	/**
	 * 发送文章消息
	 * @param array $articles
	 * @param array|null $mentionedList
	 * @param array $attributes
	 * @return array|null
	 */
	public function sendFeedCardMessage($articles, array $mentionedList = null, array $attributes = []);

}
