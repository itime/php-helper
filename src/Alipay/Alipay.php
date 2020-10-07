<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Alipay;

/**
 * 支付宝服务
 */
class Alipay{
	
	//正式模式
	const GATEWAY_URL = "https://openapi.alipay.com/gateway.do";
	
	//开发模式
	const GATEWAY_DEV_URL = "https://openapi.alipaydev.com/gateway.do";
	
	/**
	 * @var \AopClient
	 */
	private $aop;
	
	/**
	 * @var string
	 */
	private $aseKey;
	
	/**
	 * @var bool
	 */
	private $isDebug;
	
	/**
	 * Alipay constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = []){
		$this->isDebug = isset($options['debug']) ? $options['debug'] : false;
		$this->aseKey = isset($options['aes_key']) ? $options['aes_key'] : '';
		
		// 初始化支付宝请求器
		$this->initAopClient($options);
	}
	
	/**
	 * 初始化支付宝SDK框架
	 * 找到lotusphp入口文件，并初始化lotusphp
	 * lotusphp是一个第三方php框架，其主页在：lotusphp.googlecode.com
	 *
	 * @param string $sdkPath
	 * @param string $storeDir
	 * @param bool   $debug
	 * @noinspection PhpIncludeInspection
	 * @noinspection PhpUndefinedClassInspection
	 */
	public static function initFramework($sdkPath, $storeDir = null, $debug = false){
		//		$sdkPath = $this->app->getRootPath().'alipay-sdk'.DIRECTORY_SEPARATOR;
		//		$defaultStoreDir = $this->app->getRuntimePath()."alipay".DIRECTORY_SEPARATOR;
		$lotusPath = $sdkPath.'lotusphp_runtime'.DIRECTORY_SEPARATOR;
		$lotusAutoloadDir = $sdkPath.'aop';
		
		require_once $lotusPath."Lotus.php";
		
		$lotus = new \Lotus;
		$lotus->option["autoload_dir"] = $lotusAutoloadDir;
		$lotus->devMode = $debug;
		$lotus->defaultStoreDir = $storeDir;
		$lotus->init();
	}
	
	/**
	 * 初始化支付宝请求器
	 *
	 * @param array $options
	 * @noinspection PhpUndefinedClassInspection
	 */
	private function initAopClient(array $options){
		if(!class_exists('\AopClient')){
			throw new \RuntimeException('Class AopClient not found!');
		}
		
		$aop = new \AopClient ();
		$aop->gatewayUrl = $this->isDebug ? self::GATEWAY_DEV_URL : self::GATEWAY_URL;
		$aop->appId = $options['app_id'];
		
		//请填写开发者私钥去头去尾去回车，一行字符串
		$aop->rsaPrivateKey = isset($options['rsa_private_key']) ? $options['rsa_private_key'] : '';
		$aop->rsaPrivateKeyFilePath = isset($options['rsa_private_key_filepath']) ? $options['rsa_private_key_filepath'] : '';
		
		//请填写支付宝公钥，一行字符串
		$aop->alipayrsaPublicKey = isset($options['alipay_rsa_public_key']) ? $options['alipay_rsa_public_key'] : '';
		$aop->alipayPublicKey = isset($options['alipay_public_key']) ? $options['alipay_public_key'] : '';
		
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset = 'UTF-8';
		$aop->format = 'json';
		
		$this->aop = $aop;
	}
	
	/**
	 * 执行请求
	 *
	 * @param mixed  $request
	 * @param string $authToken
	 * @param string $appInfoToken
	 * @return array
	 * @throws AlipayException
	 */
	public function execute($request, $authToken = null, $appInfoToken = null){
		try{
			$response = $this->aop->execute($request, $authToken, $appInfoToken);
		}catch(\Exception $e){
			throw new AlipayServerException($e->getMessage(), $e->getCode(), $e->getPrevious());
		}
		
		if(empty($response)) {
			throw new AlipayServerException("出现错误，请查看日志获得详细错误日志。");
		}
		
		$responseNode = str_replace(".", "_", $request->getApiMethodName())."_response";
		
		/**@var $response \SimpleXMLElement|\stdClass */
		if(isset($response->error_response)){
			$response = $response->error_response;
			$errorMsg = $response->msg.($response->sub_msg ? '('.$response->sub_msg.')' : '');
			throw new AlipayResponseException($errorMsg, $response->code, $response);
		}
		
		$response = $response->$responseNode;
		if(empty($response)){
			$errorMsg = 'request alipay server fail';
			throw new AlipayServerException($errorMsg, 500);
		}
		
		if(isset($response->code) && $response->code != 10000){
			$errorMsg = $response->msg.($response->sub_msg ? '('.$response->sub_msg.')' : '');
			throw new AlipayResponseException($errorMsg, $response->code, $response);
		}
		
		return (array)$response;
	}
	
	/**
	 * 解密数据
	 *
	 * @param string $data
	 * @return string
	 * @throws AlipayException
	 */
	public function decrypt($data){
		//AES, 128 模式加密数据 CBC
		$data = base64_decode($data);
		$screct_key = base64_decode($this->aseKey);
		
		$iv_size = openssl_cipher_iv_length('AES-128-CBC');
		$iv = str_repeat("\0", $iv_size);
		
		$result = openssl_decrypt($data, 'AES-128-CBC', $screct_key, 1, $iv);
		if($result === false){
			throw new AlipayEncryptException(openssl_error_string());
		}
		
		return $result;
	}
	
	/**
	 * 加密数据
	 *
	 * @param string $data
	 * @return string
	 * @throws AlipayException
	 */
	public function rsaEncrypt($data){
		//AES, 128 模式加密数据 CBC
		$screct_key = base64_decode($this->aseKey);
		$data = trim($data);
		$data = self::addPKCS7Padding($data);
		
		//设置全0的IV
		$iv_size = openssl_cipher_iv_length('AES-128-CBC');
		$iv = str_repeat("\0", $iv_size);
		
		$encrypt_str = openssl_encrypt($data, 'AES-128-CBC', $screct_key, 0, $iv);
		if($encrypt_str === false){
			throw new AlipayEncryptException(openssl_error_string());
		}
		
		return base64_encode($encrypt_str);
	}
	
	/**
	 * 填充算法
	 *
	 * @param string $source
	 * @return string
	 */
	private static function addPKCS7Padding($source){
		$source = trim($source);
		$block = openssl_cipher_iv_length('AES-128-CBC');
		
		$pad = $block - (strlen($source) % $block);
		if($pad <= $block){
			$char = chr($pad);
			$source .= str_repeat($char, $pad);
		}
		return $source;
	}
}
