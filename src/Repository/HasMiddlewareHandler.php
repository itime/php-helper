<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Repository;

trait HasMiddlewareHandler
{

	/**
	 * @param string $class
	 * @return void
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public function setupHandler($class)
	{
		$reflect = new \ReflectionClass($class);
		$instance = $reflect->newInstance();

		if ($reflect->hasMethod('filterable')) {
			$this->filterMiddleware($reflect->getMethod('filterable')->getClosure($instance));
		}

		if ($reflect->hasMethod('detailable')) {
			$this->detailMiddleware($reflect->getMethod('detailable')->getClosure($instance));
		}

		if ($reflect->hasMethod('validateable')) {
			$this->validateMiddleware($reflect->getMethod('validateable')->getClosure($instance));
		}

		if ($reflect->hasMethod('storeable')) {
			$this->storeMiddleware($reflect->getMethod('storeable')->getClosure($instance));
		}

		if ($reflect->hasMethod('updateable')) {
			$this->updateMiddleware($reflect->getMethod('updateable')->getClosure($instance));
		}

		if ($reflect->hasMethod('deleteable')) {
			$this->deleteMiddleware($reflect->getMethod('deleteable')->getClosure($instance));
		}

		if ($reflect->hasMethod('restoreable')) {
			$this->restoreMiddleware($reflect->getMethod('restoreable')->getClosure($instance));
		}
	}

	/**
	 * @param string $class
	 * @return void
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public static function setupGlobalHandler($class)
	{
		$reflect = new \ReflectionClass($class);
		$instance = $reflect->newInstance();

		if ($reflect->hasMethod('filterable')) {
			static::globalFilterMiddleware($reflect->getMethod('filterable')->getClosure($instance));
		}

		if ($reflect->hasMethod('detailable')) {
			static::globalDetailMiddleware($reflect->getMethod('detailable')->getClosure($instance));
		}

		if ($reflect->hasMethod('validateable')) {
			static::globalValidateMiddleware($reflect->getMethod('validateable')->getClosure($instance));
		}

		if ($reflect->hasMethod('storeable')) {
			static::globalStoreMiddleware($reflect->getMethod('storeable')->getClosure($instance));
		}

		if ($reflect->hasMethod('updateable')) {
			static::globalUpdateMiddleware($reflect->getMethod('updateable')->getClosure($instance));
		}

		if ($reflect->hasMethod('deleteable')) {
			static::globalDeleteMiddleware($reflect->getMethod('deleteable')->getClosure($instance));
		}

		if ($reflect->hasMethod('restoreable')) {
			static::globalRestoreMiddleware($reflect->getMethod('restoreable')->getClosure($instance));
		}
	}

}
