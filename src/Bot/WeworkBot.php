<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bot;

/**
 * @deprecated
 */
class WeworkBot {

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * WeworkBot constructor.
	 *
	 * @param string $key
	 */
	public function __construct($key) {
		$this->key = $key;
	}

	/**
	 * 发送文本消息
	 *
	 * @param string $content
	 * @param array  $mentionedList
	 * @param array  $mentionedMobileList
	 * @return array|null
	 */
	public function sendTextMessage($content, $mentionedList = [], $mentionedMobileList = []) {
		return $this->sendMessage([
			"msgtype" => "text",
			"text" => [
				"content" => $content,
				"mentioned_list" => $mentionedList,
				"mentioned_mobile_list" => $mentionedMobileList,
			],
		]);
	}

	/**
	 * 发送Markdown消息
	 *
	 * @param string $content
	 * @return array|null
	 */
	public function sendMarkdownMessage($content) {
		return $this->sendMessage([
			"msgtype" => "markdown",
			"markdown" => [
				"content" => $content,
			],
		]);
	}

	/**
	 * 发送图片消息
	 *
	 * @param string $content
	 * @return array|null
	 */
	public function sendImageMessage($content, $fileMD5) {
		return $this->sendMessage([
			"msgtype" => "image",
			"image" => [
				"base64" => $content,
				"md5" => $fileMD5,
			],
		]);
	}

	/**
	 * 发送文章消息
	 *
	 * @param array $articles
	 * @return array|null
	 */
	public function sendNewMessage($articles) {
		return $this->sendMessage([
			"msgtype" => "news",
			"news" => [
				"articles" => $articles,
			],
		]);
	}

	/**
	 * 发送文件消息
	 *
	 * @param string $mediaId
	 * @return array|null
	 */
	public function sendFileMessage($mediaId) {
		return $this->sendMessage([
			"msgtype" => "file",
			"file" => [
				"media_id" => $mediaId,
			],
		]);
	}

	/**
	 * 发送消息
	 *
	 * @param array $data
	 * @return array|null
	 */
	public function sendMessage($data) {
		$url = "https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key={$this->key}";

		return $this->httpPostJson($url, $data);
	}

	/**
	 * 上传文件
	 *
	 * @param string $path
	 * @param string $type
	 * @return array|null
	 */
	public function uploadFile($path, $type = 'file') {
		$url = "https://qyapi.weixin.qq.com/cgi-bin/webhook/upload_media?key={$this->key}&type={$type}";

		return $this->httpUpload($url, $path);
	}

	/**
	 * 发送POST请求
	 *
	 * @param       $url
	 * @param array $data
	 * @param array $headers
	 * @return mixed|null
	 */
	protected function httpPostJson($url, $data = [], $headers = []) {
		$ch = curl_init($url);

		//设置头文件的信息作为数据流输出
		curl_setopt($ch, CURLOPT_HEADER, 0);

		// 超时设置,以秒为单位
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);

		// 超时设置，以毫秒为单位
		// curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);

		$data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$headers = array_merge($headers, [
			'Content-Type: application/json; charset=utf-8',
			// 'Content-Length: ' . strlen($data)
		]);

		// 设置请求头
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		//设置post方式提交
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		//执行命令
		$data = curl_exec($ch);

		// 显示错误信息
		if (curl_error($ch)) {
			return null;
		} else {
			// 打印返回的内容
			curl_close($ch);
		}

		return json_decode($data, true);
	}

	/**
	 * 上传文件
	 *
	 * @param string $url
	 * @param string $path
	 * @return mixed|null
	 */
	protected function httpUpload($url, $path) {
		$ch = curl_init($url);

		//设置头文件的信息作为数据流输出
		curl_setopt($ch, CURLOPT_HEADER, 0);

		// 超时设置,以秒为单位
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);

		//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		//设置post方式提交
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			'media' => new \CURLFile($path, '', basename($path)),
		]);

		//执行命令
		$data = curl_exec($ch);

		// 显示错误信息
		if (curl_error($ch)) {
			return null;
		} else {
			// 打印返回的内容
			curl_close($ch);
		}

		return json_decode($data, true);
	}

}
