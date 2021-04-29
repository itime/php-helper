<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Foundation\Wechat;

use Throwable;

class WechatResponseException extends WechatException{

	/**
	 * @var \Psr\Http\Message\ResponseInterface|\Psr\Http\Message\StreamInterface
	 */
	protected $response;

	/**
	 * WechatException constructor.
	 *
	 * @param string          $message
	 * @param int             $code
	 * @param mixed           $response
	 * @param \Throwable|null $previous
	 */
	public function __construct($message = "", $code = 0, $response = null, Throwable $previous = null){
		parent::__construct($message, $code, $previous);

		$this->response = $response;
	}

	/**
	 * @return \Psr\Http\Message\ResponseInterface|\Psr\Http\Message\StreamInterface
	 */
	public function getResponse(){
		return $this->response;
	}

}
