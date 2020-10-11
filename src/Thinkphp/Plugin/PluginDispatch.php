<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use ReflectionException;
use ReflectionMethod;
use think\App;
use think\exception\ClassNotFoundException;
use think\exception\HttpException;
use think\route\dispatch\Controller;
use Xin\Support\Str;

/**
 * Class PluginDispatch
 *
 * @property-read \think\Request|\Xin\Thinkphp\Http\RequestOptimize $request
 */
class PluginDispatch extends Controller{
	
	/**
	 * @var string
	 */
	protected $plugin;
	
	/**
	 * @var \Xin\Thinkphp\Plugin\PluginManager
	 */
	protected $pluginManager;
	
	/**
	 * @param \think\App $app
	 */
	public function init(App $app){
		$this->app = $app;
		
		// 执行路由后置操作
		$this->doRouteAfter();
		
		// 获取插件名
		$this->plugin = strip_tags($this->param['plugin']);
		
		// 获取控制器名
		$controller = strip_tags($this->param['controller']);
		if(strpos($controller, '.')){
			$pos = strrpos($controller, '.');
			$this->controller = substr($controller, 0, $pos).'.'.Str::studly(substr($controller, $pos + 1));
		}else{
			$this->controller = Str::studly($controller);
		}
		
		// 获取操作名
		$this->actionName = isset($this->param['action']) ? strip_tags($this->param['action'])
			: $this->rule->config('default_action');
		
		// 设置当前请求的插件、控制器、操作
		$this->request->setPlugin($this->plugin);
		$this->request
			->setController($this->controller)
			->setAction($this->actionName);
		
		/** @var \Xin\Thinkphp\Plugin\PluginManager $pluginManager */
		$this->pluginManager = $this->app->get('PluginManager');
	}
	
	/**
	 * @return mixed
	 */
	public function exec(){
		if(!$this->pluginManager->has($this->plugin)){
			throw new HttpException(404, "plugin {$this->plugin} not exist.");
		}
		
		try{
			// 实例化控制器
			$instance = $this->controller($this->controller);
		}catch(ClassNotFoundException $e){
			throw new HttpException(404, 'controller not exists:'.$e->getClass());
		}
		
		// 初始化视图
		$this->initView();
		
		// 注册控制器中间件
		$this->registerControllerMiddleware($instance);
		
		return $this->app->middleware->pipeline('controller')
			->send($this->request)
			->then(function() use ($instance){
				// 获取当前操作名
				$suffix = $this->rule->config('action_suffix');
				$action = $this->actionName.$suffix;
				
				if(is_callable([$instance, $action])){
					$vars = $this->request->param();
					try{
						$reflect = new ReflectionMethod($instance, $action);
						// 严格获取当前操作方法名
						$actionName = $reflect->getName();
						if($suffix){
							$actionName = substr($actionName, 0, -strlen($suffix));
						}
						
						$this->request->setAction($actionName);
					}catch(ReflectionException $e){
						$reflect = new ReflectionMethod($instance, '__call');
						$vars = [$action, $vars];
						$this->request->setAction($action);
					}
				}else{
					// 操作不存在
					throw new HttpException(404, 'method not exists:'.get_class($instance).'->'.$action.'()');
				}
				
				$data = $this->app->invokeReflectMethod($instance, $reflect, $vars);
				
				return $this->autoResponse($data);
			});
	}
	
	/**
	 * 实例化访问控制器
	 *
	 * @access public
	 * @param string $controller 控制器名称
	 * @return object
	 * @throws ClassNotFoundException
	 */
	public function controller(string $controller){
		$appName = $this->app->http->getName();
		
		$controllerLayer = $appName != $this->getDefaultAppName() ? "{$appName}controller" : 'controller';
		
		$emptyController = $this->rule->config('empty_controller') ?: 'Error';
		
		$class = $this->pluginManager->controllerClass(
			$this->plugin,
			$controller,
			$controllerLayer
		);
		
		if(class_exists($class)){
			return $this->app->make($class, [], true);
		}elseif($emptyController && class_exists(
				$emptyClass = $this->pluginManager->controllerClass(
					$this->plugin,
					$emptyController,
					$controllerLayer
				)
			)){
			return $this->app->make($emptyClass, [], true);
		}
		
		throw new ClassNotFoundException('class not exists:'.$class, $class);
	}
	
	/**
	 * 初始化视图
	 */
	protected function initView(){
		$appName = $this->app->http->getName();
		if($appName == "api"){
			return;
		}
		
		$viewLayer = $appName != $this->getDefaultAppName() ? "{$appName}view" : 'view';
		
		/** @var \think\View $view */
		$view = $this->app->make('view');
		$viewPath = $this->pluginManager->path($this->plugin).$viewLayer.DIRECTORY_SEPARATOR;
		$view->engine()->config([
			"view_path" => $viewPath,
		]);
	}
	
	/**
	 * 默认的应用名称
	 *
	 * @return string
	 */
	public function getDefaultAppName(){
		return $this->pluginManager->config('default.app_name', 'admin');
	}
	
}
