<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\View\Concerns;

use Xin\Finder\FileFinder;
use Xin\Support\Str;
use Xin\Thinkphp\View\Exceptions\TemplateNotFoundException;

trait FindTemplate {

	/**
	 * @var \Xin\Contracts\Finder\Finder
	 */
	protected $finder;

	/**
	 * @var array
	 */
	protected $appPathInits = [];

	/**
	 * 查找器
	 *
	 * @return \Xin\Contracts\Finder\Finder
	 */
	public function finder() {
		if (!$this->finder) {
			$this->initViewPath();

			$extensions = is_array($this->config['view_suffix'])
				? $this->config['view_suffix']
				: explode(',', $this->config['view_suffix']);

			$this->finder = new FileFinder(array_merge([
				$this->config['view_path'],
			], $this->config['locations']), $extensions);
		}

		return $this->finder;
	}

	/**
	 * 初始化视图路径
	 */
	protected function initViewPath() {
		if (!empty($this->config['view_path'])) {
			return;
		}

		$view = $this->config['view_dir_name'];

		if (is_dir($this->app->getAppPath() . $view)) {
			$path = $this->app->getAppPath() . $view . DIRECTORY_SEPARATOR;
		} else {
			$appName = $this->app->http->getName();
			$path = $this->app->getRootPath() . $view . DIRECTORY_SEPARATOR . ($appName ? $appName . DIRECTORY_SEPARATOR : '');
		}

		$this->config['view_path'] = $path;
	}

	/**
	 * 包含应用目录的查找器
	 *
	 * @param string $appName
	 */
	public function setFinderAppPath($appName) {
		$finder = $this->finder();

		if (!isset($this->appPathInits[$appName])) {
			$this->appPathInits[$appName] = true;

			$view = $this->config['view_dir_name'];
			$viewPath = $this->app->getBasePath() . $appName . DIRECTORY_SEPARATOR . $view . DIRECTORY_SEPARATOR;

			if (is_dir($viewPath)) {
				$path = $viewPath;
			} else {
				$path = $this->app->getRootPath() . $view . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR;
			}

			$finder->addNamespace($appName, $path);
			$finder->addLocation($path);
		}

		return $finder;
	}

	/**
	 * 自动定位模板文件
	 *
	 * @param string $template
	 * @param bool   $failException
	 * @return string
	 */
	protected function parseTemplate(string $template, $failException = true): string {
		// 分析模板文件规则
		$request = $this->app['request'];

		$depr = $this->config['view_depr'];

		$template = str_replace(['/', ':'], $depr, $template);

		// 获取视图根目录
		if (strpos($template, '@')) {
			// 跨模块调用
			$view = $this->config['view_dir_name'];
			[$appName, $template] = explode('@', $template);
		} elseif (0 !== strpos($template, '/')) {
			$controller = $request->controller();

			if (strpos($controller, '.')) {
				$pos = strrpos($controller, '.');
				$controller = substr($controller, 0, $pos) . '.' . Str::snake(substr($controller, $pos + 1));
			} else {
				$controller = Str::snake($controller);
			}

			if ($controller) {
				if ('' == $template) {
					// 如果模板文件名为空 按照默认模板渲染规则定位
					if (2 == $this->config['auto_rule']) {
						$template = $request->action(true);
					} elseif (3 == $this->config['auto_rule']) {
						$template = $request->action();
					} else {
						$template = Str::snake($request->action());
					}

					$template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
				} elseif (false === strpos($template, $depr)) {
					$template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
				}
			}
		}

		if (isset($appName)) {
			$view = $this->config['view_dir_name'];
			$viewPath = $this->app->getBasePath() . $appName . DIRECTORY_SEPARATOR . $view . DIRECTORY_SEPARATOR;

			if (is_dir($viewPath)) {
				$path = $viewPath;
			} else {
				$path = $this->app->getRootPath() . $view . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR;
			}

			$this->config['view_path'] = $path;
		} else {
			$path = $this->config['view_path'];
		}

		$templateFile = $path . ltrim($template, '/') . '.' . ltrim($this->config['view_suffix'], '.');

		if (is_file($templateFile)) {
			// 记录模板文件的更新时间
			$this->includeFile[$templateFile] = filemtime($templateFile);
		} elseif ($failException) {
			throw new TemplateNotFoundException('template not exists:' . $templateFile);
		}

		return $templateFile;
	}

	/**
	 * 分析加载的模板文件并读取内容 支持多个模板文件读取
	 *
	 * @param string $templateName 模板文件名
	 * @return string
	 * @throws \Exception
	 */
	protected function parseTemplateName(string $templateName): string {
		$array = explode(',', $templateName);
		$parseStr = '';

		foreach ($array as $templateName) {
			if (empty($templateName)) {
				continue;
			}

			if (0 === strpos($templateName, '$')) {
				//支持加载变量文件名
				$templateName = $this->get(substr($templateName, 1));
			}

			$template = $this->parseTemplateFile($templateName);

			if ($template) {
				// 获取模板文件内容
				$parseStr .= file_get_contents($template);
			}
		}

		return $parseStr;
	}

	/**
	 * 解析模板文件名
	 *
	 * @access private
	 * @param string $template 文件名
	 * @return string
	 * @throws TemplateNotFoundException
	 */
	protected function parseTemplateFile(string $template): string {
		if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
			if (0 !== strpos($template, '/')) {
				$template = str_replace(['/', ':'], $this->config['view_depr'], $template);
			} else {
				$template = str_replace(['/', ':'], $this->config['view_depr'], substr($template, 1));
			}

			// $template = $this->config['view_path'].$template.'.'.ltrim($this->config['view_suffix'], '.');

			// 是否跨模块调用视图
			if ($pos = strpos($template, '@')) {
				$app = substr($template, 0, $pos);
				$this->setFinderAppPath($app);
			}

			$template = $this->finder()->find($template);
		}

		if (is_file($template)) {
			// 记录模板文件的更新时间
			$this->includeFile[$template] = filemtime($template);

			return $template;
		}

		throw new TemplateNotFoundException('template not exists:' . $template);
	}

	/**
	 * 检测是否存在模板文件
	 *
	 * @access public
	 * @param string $template 模板文件或者模板规则
	 * @return bool
	 */
	public function exists(string $template): bool {
		if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
			// 获取模板文件名
			$template = $this->parseTemplate($template, false);
		}

		return is_file($template);
	}

}
