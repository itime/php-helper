<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Repository;

use Xin\Middleware\MiddlewareManager;

trait HasMiddleware
{

	use HasMiddlewareHandler;

	/**
	 * @var MiddlewareManager
	 */
	protected static $globalMiddlewareManager;

	/**
	 * @var MiddlewareManager
	 */
	protected $middlewareManager;

	/**
	 * 调用中间件
	 * @param mixed $input
	 * @param callable $destination
	 * @param string $name
	 * @return mixed
	 */
	protected function middleware($input, callable $destination, $name)
	{
		return static::globalMiddlewareManager()->then($input, function ($input) use ($destination, $name) {
			return $this->middlewareManager->then($input, $destination, $name);
		}, $name);
	}

	/**
	 * @param string $name
	 * @param \Closure $closure
	 * @return HasMiddleware
	 */
	public function registerMiddleware($name, \Closure $closure)
	{
		$this->middlewareManager->push($closure, $name);

		return $this;
	}

	/**
	 * 添加一个过滤中间件
	 * @param \Closure $closure
	 * @return $this
	 */
	public function filterMiddleware(\Closure $closure)
	{
		$this->registerMiddleware(static::SCENE_FILTER, $closure);

		return $this;
	}

	/**
	 * 添加一个详情中间件
	 * @param \Closure $closure
	 * @return $this
	 */
	public function detailMiddleware(\Closure $closure)
	{
		$this->registerMiddleware(static::SCENE_DETAIL, $closure);

		return $this;
	}

	/**
	 * 添加一个验证中间件
	 * @param \Closure $closure
	 * @return $this
	 */
	public function validateMiddleware(\Closure $closure)
	{
		$this->registerMiddleware(static::SCENE_VALIDATE, $closure);

		return $this;
	}

	/**
	 * 添加一个存储中间件
	 * @param \Closure $closure
	 * @return $this
	 */
	public function storeMiddleware(\Closure $closure)
	{
		$this->registerMiddleware(static::SCENE_STORE, $closure);

		return $this;
	}

	/**
	 * 添加一个更新中间件
	 * @param \Closure $closure
	 * @return $this
	 */
	public function updateMiddleware(\Closure $closure)
	{
		$this->registerMiddleware(static::SCENE_UPDATE, $closure);

		return $this;
	}

	/**
	 * 添加一个删除中间件
	 * @param \Closure $closure
	 * @return $this
	 */
	public function deleteMiddleware(\Closure $closure)
	{
		$this->registerMiddleware(static::SCENE_DELETE, $closure);

		return $this;
	}

	/**
	 * 添加一个数据恢复中间件
	 * @param \Closure $closure
	 * @return $this
	 */
	public function restoreMiddleware(\Closure $closure)
	{
		$this->registerMiddleware(static::SCENE_RESTORE, $closure);

		return $this;
	}

	/**
	 * @return \Xin\Middleware\MiddlewareManager
	 */
	public function getMiddlewareManager()
	{
		return $this->middlewareManager;
	}

	/**
	 * @param \Xin\Middleware\MiddlewareManager $middlewareManager
	 */
	public function setMiddlewareManager(MiddlewareManager $middlewareManager)
	{
		$this->middlewareManager = $middlewareManager;
	}

	/**
	 * 注册中间件
	 * @param string $name
	 * @param \Closure $closure
	 */
	public static function registerGlobalMiddleware($name, \Closure $closure)
	{
		static::globalMiddlewareManager()->push($closure, $name);
	}

	/**
	 * 获取全局中间件
	 * @return MiddlewareManager
	 */
	public static function globalMiddlewareManager()
	{
		if (static::$globalMiddlewareManager === null) {
			static::$globalMiddlewareManager = new MiddlewareManager();
		}

		return static::$globalMiddlewareManager;
	}

	/**
	 * @param \Closure $closure
	 */
	public static function globalFilterMiddleware(\Closure $closure)
	{
		static::registerGlobalMiddleware(static::SCENE_FILTER, $closure);
	}

	/**
	 * @param \Closure $closure
	 */
	public static function globalDetailMiddleware(\Closure $closure)
	{
		static::registerGlobalMiddleware(static::SCENE_DETAIL, $closure);
	}

	/**
	 * @param \Closure $closure
	 */
	public static function globalValidateMiddleware(\Closure $closure)
	{
		static::registerGlobalMiddleware(static::SCENE_VALIDATE, $closure);
	}

	/**
	 * @param \Closure $closure
	 */
	public static function globalStoreMiddleware(\Closure $closure)
	{
		static::registerGlobalMiddleware(static::SCENE_STORE, $closure);
	}

	/**
	 * @param \Closure $closure
	 */
	public static function globalUpdateMiddleware(\Closure $closure)
	{
		static::registerGlobalMiddleware(static::SCENE_UPDATE, $closure);
	}

	/**
	 * @param \Closure $closure
	 */
	public static function globalDeleteMiddleware(\Closure $closure)
	{
		static::registerGlobalMiddleware(static::SCENE_DELETE, $closure);
	}

	/**
	 * @param \Closure $closure
	 */
	public static function globalRestoreMiddleware(\Closure $closure)
	{
		static::registerGlobalMiddleware(static::SCENE_RESTORE, $closure);
	}

}
