<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Http;

/**
 * Class Client
 * @method Response get($url, $data = null, $options = [])
 * @method Response post($url, $data = null, $options = [])
 * @method Response put($url, $data = null, $options = [])
 * @method Response delete($url, $data = null, $options = [])
 * @method Response upload($url, $data = null, $options = [])
 */
class Client{
	
	/**
	 * @var array
	 */
	protected $options = [];
	
	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $client = null;
	
	/**
	 * @var static
	 */
	protected static $instance = null;
	
	/**
	 * Client constructor.
	 *
	 * @param array $options
	 */
	public function __construct($options = []){
		$this->options = $options;
		$this->client = new \GuzzleHttp\Client($options);
	}
	
	/**
	 * 发起请求
	 *
	 * @param string $method
	 * @param string $url
	 * @param mixed  $data
	 * @param array  $options
	 * @return \Xin\Http\Response
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function request($method, $url, $data = null, $options = []){
		$method = strtoupper($method);
		
		if($data){
			if('POST' == $method){
				$options['form_params'] = $data;
			}elseif('PUT' == $method){
				$options['form_params'] = $data;
			}elseif('UPLOAD' == $method){
				$options['multipart'] = $data;
			}elseif('DELETE' == $method){
				$options['query'] = $data;
			}else{
				$options['query'] = $data;
			}
		}
		
		$response = $this->client->request($method, $url, $options);

		return new Response($response);
	}
	
	/**
	 * 获取默认实例
	 *
	 * @return static
	 */
	public static function instance(){
		if(static::$instance === null){
			static::$instance = new static([
				'timeout'         => 30,
				'connect_timeout' => 5,
				'allow_redirects' => [
					'max'             => 5,
					'strict'          => false,
					'referer'         => true,
					'protocols'       => ['http', 'https'],
					'track_redirects' => false,
				],
			]);
		}
		
		return static::$instance;
	}
	
	/**
	 * 动态方法
	 *
	 * @param string $name
	 * @param array  $arguments
	 * @return false|mixed
	 */
	public function __call($name, $arguments){
		array_unshift($arguments, $name);
		return call_user_func_array([$this, 'request'], $arguments);
	}
	
	/**
	 * 静态调用动态方法
	 *
	 * @param string $name
	 * @param array  $arguments
	 * @return false|mixed
	 */
	public static function __callStatic($name, $arguments){
		return call_user_func_array([static::instance(), $name], $arguments);
	}
	
}
