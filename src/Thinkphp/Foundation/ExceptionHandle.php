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
use Xin\Thinkphp\Facade\Hint;
use Xin\Wechat\Exceptions\WechatException;

/**
 * Class ExceptionHandle
 * @method void reportTo(\Throwable $e, string $log)
 */
class ExceptionHandle extends Handle
{

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
	 * @inheritDoc
	 */
	public function report(\Throwable $exception): void
	{
		if ($this->isIgnoreReport($exception)) {
			return;
		}

		// 收集异常数据
		$data = [
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'message' => $this->getMessage($exception),
			'code' => $this->getCode($exception),
		];

		$traceString = $exception->getTraceAsString();
		if ($find = strpos($traceString, "\n#11 ")) {
			$traceString = substr($traceString, 0, $find);
		}
		$log = "[{$data['message']}]: code({$data['code']}): {$data['file']}({$data['line']})\n{$traceString}";

		if ($this->app->config->get('log.record_trace')) {
			$log .= PHP_EOL . $exception->getTraceAsString();
		}

		try {
			$this->app->log->record($log, 'error');
		} catch (\Throwable $e) {
		}

		if (method_exists($this, 'reportTo')) {
			try {
				$this->reportTo($exception, $log);
			} catch (\Throwable $e) {
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function render($request, \Throwable $e): Response
	{
		// 参数验证错误
		if ($e instanceof ValidateException) {
			return Hint::error($e->getMessage(), 400);
		} elseif ($e instanceof AuthenticationException) {
			return $this->authenticationHandle($e);
		} elseif ($e instanceof ModelNotFoundException) {
			$title = get_const_value($e->getModel(), 'TITLE');
			$e = new HttpException(404, ($title ?: '数据') . "不存在！");
		} elseif ($e instanceof DataNotFoundException) {
			$e = new HttpException(404, "数据不存在！");
		} elseif ($e instanceof WechatException) {
			return Hint::error($e->getMessage(), $e->getCode());
		}

		// 其他错误交给系统处理
		return parent::render($request, $e);
	}

	/**
	 * 未授权处理
	 *
	 * @param \Xin\Auth\AuthenticationException $e
	 * @return \think\Response
	 */
	protected function authenticationHandle(AuthenticationException $e): Response
	{
		if ($this->isJson()) {
			return Hint::error("登录已失效", -1, $e->redirectTo());
		}

		return redirect($e->redirectTo());
	}

	/**
	 * @inheritDoc
	 */
	protected function renderHttpException(HttpException $e): Response
	{
		$statusCode = $e->getStatusCode();

		if ($this->isJson() && in_array($statusCode, [403, 404])) {
			$msg = $e->getMessage();
			if (empty($msg)) {
				$msg = strval($statusCode);
			}

			return Hint::error($msg, $statusCode);
		}

		return parent::renderHttpException($e);
	}

	/**
	 * @inheritDoc
	 */
	protected function convertExceptionToResponse(\Throwable $exception): Response
	{
		if (!$this->isJson()) {
			return parent::convertExceptionToResponse($exception);
		}

		// 收集异常数据
		$code = $this->getCode($exception);
		$msg = $this->getMessage($exception);

		// 不显示详细错误信息
		if (!$this->app->isDebug() && !$this->app->config->get('app.show_error_msg')
			&& !$exception instanceof \LogicException) {
			$msg = $this->app->config->get('app.error_message');
		}

		// 调试模式，获取详细的错误信息
		$extend = $this->app->isDebug() ? [
			'name' => get_class($exception),
			'message' => $this->getMessage($exception),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'trace' => $exception->getTrace(),
			'source' => $this->getSourceCode($exception),
			'datas' => $this->getExtendData($exception),
			'tables' => [
				'GET Data' => $_GET,
				'POST Data' => $_POST,
				'Files' => $_FILES,
				'Cookies' => $_COOKIE,
				'Session' => isset($_SESSION) ? $_SESSION : [],
				'Server/Request Data' => $_SERVER,
				'Environment Variables' => $_ENV,
			],
		] : [];

		$response = Hint::error($msg, $code, null, $extend);

		if ($exception instanceof HttpException) {
			$statusCode = $exception->getStatusCode();
			$response->header($exception->getHeaders());
		}

		return $response->code($statusCode ?? 500);
	}

	/**
	 * 是否Ajax请求
	 *
	 * @return bool
	 */
	protected function isJson()
	{
		return $this->app->http->getName() === 'api'
			|| (method_exists($this->app->request, 'expectsJson') && $this->app->request->expectsJson())
			|| ($this->app->request->isAjax() || $this->app->request->isJson());
	}

}
