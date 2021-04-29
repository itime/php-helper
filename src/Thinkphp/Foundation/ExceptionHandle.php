<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Xin\Auth\AuthenticationException;
use Xin\Foundation\Wechat\WechatException;
use Xin\Thinkphp\Facade\Hint;

class ExceptionHandle extends Handle{

	/**
	 * 不需要记录信息（日志）的异常类列表
	 *
	 * @var array
	 */
	protected $ignoreReport = [
		HttpException::class,
		HttpResponseException::class,
		ModelNotFoundException::class,
		DataNotFoundException::class,
		ValidateException::class,
		AuthenticationException::class,
	];

	/**
	 * 记录异常信息（包括日志或者其它方式记录）
	 *
	 * @access public
	 * @param \Throwable $exception
	 * @return void
	 */
	public function report(\Throwable $exception):void{
		if($this->isIgnoreReport($exception)){
			return;
		}

		// 收集异常数据
		$data = [
			'file'    => $exception->getFile(),
			'line'    => $exception->getLine(),
			'message' => $this->getMessage($exception),
			'code'    => $this->getCode($exception),
		];

		$traceString = $exception->getTraceAsString();
		if($find = strpos($traceString, "\n#11 ")){
			$traceString = substr($traceString, 0, $find);
		}
		$log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]\n{$traceString}";

		if($this->app->config->get('log.record_trace')){
			$log .= PHP_EOL.$exception->getTraceAsString();
		}

		try{
			$this->app->log->record($log, 'error');
		}catch(\Exception $e){
		}
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @access public
	 * @param \think\Request $request
	 * @param \Throwable     $e
	 * @return Response
	 */
	public function render($request, \Throwable $e):Response{
		// 参数验证错误
		if($e instanceof ValidateException){
			return Hint::error($e->getMessage(), 400);
		}elseif($e instanceof AuthenticationException){
			return $this->authenticationHandle($e);
		}elseif($e instanceof ModelNotFoundException){
			$title = get_const_value($e->getModel(), 'TITLE');
			$e = new HttpException(404, ($title ?: '数据')."不存在！");
		}elseif($e instanceof DataNotFoundException){
			$e = new HttpException(404, "数据不存在！");
		}elseif($e instanceof WechatException){
			return Hint::error($e->getMessage(), $e->getCode());
		}

		// 其他错误交给系统处理
		return parent::render($request, $e);
	}

	/**
	 * 未授权处理
	 *
	 * @param \Xin\Auth\AuthenticationException $e
	 * @return \Symfony\Component\HttpFoundation\Response|\think\Response
	 */
	protected function authenticationHandle(AuthenticationException $e){
		if($this->isJson()){
			return Hint::error("登录已失效", -1, $e->redirectTo());
		}else{
			return redirect($e->redirectTo());
		}
	}

	/**
	 * @access protected
	 * @param HttpException $e
	 * @return Response
	 */
	protected function renderHttpException(HttpException $e):Response{
		$statusCode = $e->getStatusCode();

		if($this->isJson() && in_array($statusCode, [403, 404])){
			$msg = $e->getMessage();
			if(empty($msg)){
				$msg = strval($statusCode);
			}

			return Hint::error($msg, $statusCode);
		}

		return parent::renderHttpException($e);
	}

	/**
	 * @access protected
	 * @param \Throwable $exception
	 * @return Response
	 */
	protected function convertExceptionToResponse(\Throwable $exception):Response{
		if(!$this->isJson()){
			return parent::convertExceptionToResponse($exception);
		}

		// 收集异常数据
		$code = $this->getCode($exception);
		$msg = $this->getMessage($exception);

		// 不显示详细错误信息
		if(!$this->app->isDebug() && !$this->app->config->get('app.show_error_msg')
			&& !$exception instanceof \LogicException){
			$msg = $this->app->config->get('app.error_message');
		}

		// 调试模式，获取详细的错误信息
		$extend = $this->app->isDebug() ? [
			'name'   => get_class($exception),
			'file'   => $exception->getFile(),
			'line'   => $exception->getLine(),
			'trace'  => $exception->getTrace(),
			'source' => $this->getSourceCode($exception),
			'datas'  => $this->getExtendData($exception),
			'tables' => [
				'GET Data'              => $_GET,
				'POST Data'             => $_POST,
				'Files'                 => $_FILES,
				'Cookies'               => $_COOKIE,
				'Session'               => isset($_SESSION) ? $_SESSION : [],
				'Server/Request Data'   => $_SERVER,
				'Environment Variables' => $_ENV,
			],
		] : [];

		return Hint::error($msg, $code, null, $extend);
	}

	/**
	 * 是否Ajax请求
	 *
	 * @return bool
	 */
	protected function isJson(){
		return $this->app->http->getName() === 'api'
			|| $this->app->request->isAjax()
			|| $this->app->request->isJson();
	}

}
