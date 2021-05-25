<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Http;

/**
 * Class Response
 *
 * @mixin \GuzzleHttp\Psr7\Response
 */
class Response{

	/**
	 * @var \Psr\Http\Message\ResponseInterface
	 */
	protected $response;

	/**
	 * @var \GuzzleHttp\Exception\BadResponseException
	 */
	protected $e;

	/**
	 * Response constructor.
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response
	 */
	public function __construct($response, $e = null){
		$this->response = $response;
		$this->e = $e;
	}

	/**
	 * 获取响应内容
	 *
	 * @return string
	 */
	public function getContents(){
		return $this->response->getBody()->getContents();
	}

	/**
	 * 响应是否为200
	 *
	 * @return bool
	 */
	public function isOk(){
		return $this->getStatusCode() == 200;
	}

	/**
	 * json 解析
	 *
	 * @param bool $associative
	 * @return array|\stdClass
	 */
	public function json($associative = true){
		return json_decode($this->getContents(), $associative);
	}

	/**
	 * xml 解析
	 *
	 * @param bool $associative
	 * @return array
	 */
	public function xml($associative = true){
		//将XML转为array,禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		return json_decode(json_encode(simplexml_load_string($this->getContents(), 'SimpleXMLElement', LIBXML_NOCDATA)), $associative);
	}

	/**
	 * 获取相应内容类型
	 *
	 * @return bool
	 */
	public function getContentType(){
		return $this->getHeaderLine('Content-Type');
	}

	/**
	 * 响应是哪个类型
	 *
	 * @param string $contentType
	 * @return bool
	 */
	public function isContentType($contentType){
		return stripos($this->getContentType(), $contentType) !== false;
	}

	/**
	 * 是否为JSON响应
	 *
	 * @return bool
	 */
	public function isJson(){
		return $this->isContentType("application/json");
	}

	/**
	 * 是否为XML响应
	 *
	 * @return bool
	 */
	public function isXml(){
		return $this->isContentType("application/xml");
	}

	/**
	 * 响应解析为数组
	 *
	 * @return array
	 */
	public function toArray(){
		if($this->isXml()){
			return (array)$this->xml();
		}

		return (array)$this->json();
	}

	/**
	 * 获取原始的响应信息
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getRaw(){
		return $this->response;
	}

	/**
	 * 如果有异常则抛出
	 *
	 * @return $this
	 * @throws \GuzzleHttp\Exception\BadResponseException
	 */
	public function throwException(){
		if($this->e){
			throw $this->e;
		}

		return $this;
	}

	/**
	 * 当前响应是否异常
	 *
	 * @return bool
	 */
	public function isException(){
		return $this->e != null;
	}

	/**
	 * 动态调用原始响应的方法
	 *
	 * @param string $name
	 * @param array  $arguments
	 * @return false|mixed
	 */
	public function __call($name, $arguments){
		return call_user_func_array([$this->response, $name], $arguments);
	}

}
