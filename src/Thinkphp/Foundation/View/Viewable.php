<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\View;

/**
 * @property-read \think\App app
 */
trait Viewable{
	
	/**
	 * 模板变量赋值
	 *
	 * @param string|array $name 模板变量
	 * @param mixed        $value 变量值
	 * @return $this
	 */
	protected function assign($name, $value = null){
		$this->app['view']->assign($name, $value);
		
		return $this;
	}
	
	/**
	 * 解析和获取模板内容 用于输出
	 *
	 * @param string $template 模板文件名或者内容
	 * @param array  $vars 模板变量
	 * @return string
	 * @noinspection PhpUnhandledExceptionInspection
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	protected function fetch(string $template = '', array $vars = []){
		return $this->app['view']->fetch($template, $vars);
	}
	
	/**
	 * 解析和获取模板内容 用于输出
	 *
	 * @param string $template 模板文件名或者内容
	 * @param array  $vars 模板变量
	 * @return string
	 */
	protected function display(string $template = '', array $vars = []){
		return $this->app['view']->display($template, $vars);
	}
}
