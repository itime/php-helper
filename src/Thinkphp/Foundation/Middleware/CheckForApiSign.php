<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Middleware;

use think\App;
use think\exception\HttpException;
use think\Request;
use Xin\Support\Arr;
use Xin\Support\Str;

class CheckForApiSign
{

	/**
	 * @var \think\App
	 */
	protected $app;

	/**
	 * @var string
	 */
	protected static $secretKey;

	/**
	 * @param \think\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * 检查站点是否允许访问
	 *
	 * @param \think\Request $request
	 * @param \Closure $next
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle(Request $request, \Closure $next)
	{
		if ($this->app->config->get('app.env') != 'local') {
			self::setSecretKey($request->app('access_key'));
			$this->check($request);
		}

		return $next($request);
	}

	/**
	 * 验证签名是否正确
	 *
	 * @param \think\Request $request
	 */
	protected function check(Request $request)
	{
		$timestamp = $request->get('timestamp');
		if ($timestamp < $request->time() - 600) {
			throw new HttpException(404, '页面不存在！');
		}

		$data = [];
		if (!$request->isGet()) {
			$method = $request->method();
			$data = $request->$method();
		}

		$data['access_id'] = $request->get('access_id');
		$data['timestamp'] = $timestamp;

		$sign = $this->resolveSign($request);
		$signType = $this->resolveSignType($request);
		$makeSign = $this->makeSign($data, $signType);

		if ($makeSign != $sign) {
			throw new \LogicException("sign invalid.");
		}
	}

	/**
	 * 解析签名字符串
	 *
	 * @param \think\Request $request
	 * @return string
	 */
	protected function resolveSign(Request $request)
	{
		$sign = $request->get('sign');
		if (empty($sign)) {
			$sign = $request->header('sign');
		}

		if (empty($sign) || !is_string($sign)) {
			throw new \LogicException("sign invalid.");
		}

		return $sign;
	}

	/**
	 * 解析签名类型
	 *
	 * @param \think\Request $request
	 * @return array|mixed|string
	 */
	protected function resolveSignType(Request $request)
	{
		$signType = $request->get('sign_type');
		if (empty($signType)) {
			$signType = $request->header('sign_type');
		}

		if (empty($signType)) {
			$signType = 'md5';
		}

		return $signType;
	}

	/**
	 * 加密密钥
	 */
	protected static function secretKey()
	{
		return self::$secretKey;
	}

	/**
	 * 生成签名数据
	 *
	 * @param string $key
	 * @param array $data
	 * @param string $signType
	 * @return string
	 */
	protected function makeSign($data, $signType)
	{
		Arr::sort($data);
		$queryString = Str::buildUrlQuery($data) . static::secretKey();

		return md5($queryString);
	}

	/**
	 * 设置密钥
	 *
	 * @param string $secretKey
	 */
	public static function setSecretKey($secretKey)
	{
		self::$secretKey = $secretKey;
	}

}
