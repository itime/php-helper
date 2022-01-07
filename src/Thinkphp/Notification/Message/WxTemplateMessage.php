<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Notification\Message;

use Xin\Support\Contracts\Arrayable;

class WxTemplateMessage implements Arrayable {

	/**
	 * @var string
	 */
	public $openid;

	/**
	 * @var string
	 */
	public $templateId;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var array
	 */
	public $miniprogram;

	/**
	 * @var array
	 */
	public $data;

	/**
	 * @return string
	 */
	public function getOpenid() {
		return $this->openid;
	}

	/**
	 * @param string $openid
	 * @return $this
	 */
	public function setOpenid($openid) {
		$this->openid = $openid;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTemplateId() {
		return $this->templateId;
	}

	/**
	 * @param string $templateId
	 * @return $this
	 */
	public function setTemplateId($templateId) {
		$this->templateId = $templateId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 * @return $this
	 */
	public function setUrl($url) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getMiniprogram() {
		return $this->miniprogram;
	}

	/**
	 * @param array $miniprogram
	 * @return $this
	 */
	public function setMiniprogram(array $miniprogram) {
		$this->miniprogram = $miniprogram;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 * @return $this
	 */
	public function setData(array $data) {
		$this->data = $data;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function toArray() {
		$data = [
			'touser' => $this->openid,
			'template_id' => $this->templateId,
			'data' => $this->data,
		];

		if (!empty($this->url)) {
			$data['url'] = $this->url;
		}

		if (!empty($this->miniprogram)) {
			$data['miniprogram'] = $this->miniprogram;
		}

		return $data;
	}

}
