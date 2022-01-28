<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Hint;

use Xin\Capsule\Manager;
use Xin\Contracts\Hint\Factory as HintFactory;
use Xin\Support\Arr;

/**
 * Class HintManager
 * @mixin Hint
 */
class HintManager extends Manager implements HintFactory
{

	/**
	 * @var \Closure
	 */
	protected $autoResolverCallback;

	/**
	 * 获取提示器
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Hint\Hint
	 */
	public function hint($name = null)
	{
		return $this->driver($name);
	}

	/**
	 * 强制使用Api模式
	 *
	 * @return $this|\Xin\Hint\HintManager
	 */
	public function shouldUseApi()
	{
		return $this->shouldUse('api');
	}

	/**
	 * 强制使用Web模式
	 *
	 * @return $this|\Xin\Hint\HintManager
	 */
	public function shouldUseWeb()
	{
		return $this->shouldUse('web');
	}

	/**
	 * 使用指定的提示器
	 *
	 * @return $this|\Xin\Hint\HintManager
	 */
	public function shouldUse($name)
	{
		$this->setDefaultDriver($name);

		return $this;
	}

	/**
	 * 设置自动完成提示器
	 *
	 * @param \Closure $resolverCallback
	 */
	public function setAutoResolver(\Closure $resolverCallback)
	{
		$this->autoResolverCallback = $resolverCallback;
	}

	/**
	 * @inerhitDoc
	 */
	protected function getDefaultDriver()
	{
		if (is_callable($this->autoResolverCallback)) {
			return call_user_func($this->autoResolverCallback);
		}

		return $this->getConfig('defaults.hint', 'api');
	}

	/**
	 * @inerhitDoc
	 */
	protected function setDefaultDriver($name)
	{
		$this->setConfig('defaults.hint', $name);
	}

	public function getDriverConfig($name)
	{
		return $this->getConfig($name ? "hints.{$name}" : 'hints');
	}

	/**
	 * @return string
	 */
	protected function getDefault($type)
	{
		return Arr::get($this->config, "defaults.{$type}", 'default');
	}

}
