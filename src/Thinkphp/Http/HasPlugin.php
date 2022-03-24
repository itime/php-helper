<?php

namespace Xin\Thinkphp\Http;

trait HasPlugin
{
	/**
	 * 插件
	 *
	 * @var string
	 */
	protected $plugin;

	/**
	 * 获取当前的模块名
	 *
	 * @access public
	 * @return string
	 */
	public function plugin(): string
	{
		return $this->plugin ?: '';
	}

	/**
	 * 设置当前的插件名
	 *
	 * @access public
	 * @param string $plugin 插件名
	 * @return $this
	 */
	public function setPlugin(string $plugin): self
	{
		$this->plugin = $plugin;

		return $this;
	}
}