<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\View;

use Xin\Finder\FileFinder;

/**
 * Trait Finder
 *
 * @mixin \Xin\Thinkphp\View\Template
 */
trait TemplateFinder{

	/**
	 * @var \Xin\Finder\FileFinderInterface
	 */
	protected $finder;

	/**
	 * @var array
	 */
	protected $appPathsInit = [];

	/**
	 * 解析模板文件名
	 *
	 * @access private
	 * @param string $template 文件名
	 * @return string
	 * @throws TemplateNotFoundException
	 */
	public function findView(string $template):string{
		// 模板不存在 抛出异常
		if('' != pathinfo($template, PATHINFO_EXTENSION) && !is_file($template)){
			throw new TemplateNotFoundException('template not exists:'.$template, $template);
		}

		if(0 !== strpos($template, '/')){
			$template = str_replace(['/', ':'], $this->config['view_depr'], $template);
		}else{
			$template = str_replace(['/', ':'], $this->config['view_depr'], substr($template, 1));
		}

		return $this->findViewHintIf($template, function($template){
			$template = $this->finder()->find($template);

			// 记录模板文件的更新时间
			$this->includeFile[$template] = filemtime($template);

			return $template;
		});
	}

	/**
	 * 检查模板是否是跨模块
	 *
	 * @param string   $template
	 * @param callable $callback
	 * @return string
	 */
	public function findViewHintIf($template, callable $callback){
		// 是否跨模块调用视图
		$findAppIndex = strpos($template, '@');
		if($findAppIndex){
			$app = substr($template, 0, $findAppIndex);
			return $this->finderWithAppPath($app)->find($template);
		}

		return $callback($template);
	}

	/**
	 * 查找器
	 *
	 * @return \Xin\Finder\FileFinderInterface
	 */
	public function finder(){
		if(!$this->finder){
			$this->initViewPath();

			$extensions = is_array($this->config['view_suffix'])
				? $this->config['view_suffix']
				: explode(',', $this->config['view_suffix']);

			$this->finder = new FileFinder([
				$this->config['view_path'],
			], $extensions);
		}

		return $this->finder;
	}

	/**
	 * 初始化视图路径
	 */
	protected function initViewPath(){
		if(!empty($this->config['view_path'])){
			return;
		}

		$view = $this->config['view_dir_name'];

		if(is_dir($this->app->getAppPath().$view)){
			$path = $this->app->getAppPath().$view.DIRECTORY_SEPARATOR;
		}else{
			$appName = $this->app->http->getName();
			$path = $this->app->getRootPath().$view.DIRECTORY_SEPARATOR.($appName ? $appName.DIRECTORY_SEPARATOR : '');
		}

		$this->config['view_path'] = $path;
	}

	/**
	 * 包含应用目录的查找器
	 *
	 * @param string $appName
	 * @return \Xin\Finder\FileFinderInterface
	 */
	public function finderWithAppPath($appName){
		$finder = $this->finder();

		if(!isset($this->appPathsInit[$appName])){
			$view = $this->config['view_dir_name'];
			$viewPath = $this->app->getBasePath().$appName.DIRECTORY_SEPARATOR.$view.DIRECTORY_SEPARATOR;

			if(is_dir($viewPath)){
				$path = $viewPath;
			}else{
				$path = $this->app->getRootPath().$view.DIRECTORY_SEPARATOR.$appName.DIRECTORY_SEPARATOR;
			}

			$finder->addNamespace($appName, $path);
			$finder->addLocation($path);
		}

		return $finder;
	}

}
