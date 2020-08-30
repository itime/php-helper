<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Foundation;

use think\App;
use Xin\Contracts\Foundation\Application;

class OS implements Application{
	
	/**
	 * @var \think\App
	 */
	protected $app;
	
	/**
	 * @var \Closure
	 */
	protected $isCustomDevelopCallback;
	
	/**
	 * @var array
	 */
	protected $developDomainList = [];
	
	/**
	 * OS constructor.
	 *
	 * @param \think\App $app
	 */
	public function __construct(App $app){
		$this->app = $app;
	}
	
	/**
	 * @inheritDoc
	 */
	public function isEnv(...$env){
		return in_array($this->app->config->get('app.env'), $env);
	}
	
	/**
	 * @inheritDoc
	 */
	public function isLocal(){
		return $this->isEnv('local');
	}
	
	/**
	 * @inheritDoc
	 */
	public function isDevelop(){
		return $this->isEnv('dev', 'develop') || $this->isCustomDevelop();
	}
	
	/**
	 * @inheritDoc
	 */
	public function isProduction(){
		return $this->isEnv('pro', 'production');
	}
	
	/**
	 * 是否是开发模式
	 *
	 * @return bool
	 */
	public function isCustomDevelop(){
		$result = null;
		if($this->isCustomDevelopCallback){
			$result = call_user_func($this->isCustomDevelopCallback, $this);
		}
		
		if($result !== null){
			return $result;
		}
		
		return in_array($this->app->request->domain(), $this->developDomainList);
	}
	
	/**
	 * @inheritDoc
	 */
	public function version(){
		return $this->app->version();
	}
	
	/**
	 * 优化系统路径
	 *
	 * @param string $path
	 * @return string
	 */
	protected function optimizePath($path){
		return $path ? $path.DIRECTORY_SEPARATOR : $path;
	}
	
	/**
	 * 优化Web路径
	 *
	 * @param string $path
	 * @return string
	 */
	protected function optimizeWebPath($path){
		return $path ? $path.'/' : $path;
	}
	
	/**
	 * @inheritDoc
	 */
	public function rootPath($path = null){
		return $this->app->getRootPath().$this->optimizePath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function webRootPath($path = null){
		return $this->rootPath('public').$this->optimizePath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function storagePath($path = null){
		return $this->rootPath('storage').$this->optimizePath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function pluginPath($path = null){
		return $this->rootPath('storage').$this->optimizePath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function runtimePath($path = null){
		return $this->app->getRuntimePath().$this->optimizePath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assetVendorPath($path = null){
		return '/vendor/'.$this->optimizeWebPath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assetScopePath($path = null){
		$appName = $this->app->http->getName();
		return "/{$appName}/".$this->optimizeWebPath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assetImagesPath($path = null){
		return $this->assetScopePath('images').$this->optimizeWebPath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assetScriptsPath($path = null){
		return $this->assetScopePath('js').$this->optimizeWebPath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assetStylesPath($path = null){
		return $this->assetScopePath('css').$this->optimizeWebPath($path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assetFontsPath($path = null){
		return $this->assetScopePath('fonts').$this->optimizeWebPath($path);
	}
	
	/**
	 * @return \Closure
	 */
	public function getIsCustomDevelopCallback(){
		return $this->isCustomDevelopCallback;
	}
	
	/**
	 * @param \Closure $customDevelopCallback
	 */
	public function setIsCustomDevelopCallback(\Closure $customDevelopCallback){
		$this->isCustomDevelopCallback = $customDevelopCallback;
	}
	
	/**
	 * @return array
	 */
	public function getDevelopDomainList(){
		return $this->developDomainList;
	}
	
	/**
	 * @param array $developDomainList
	 */
	public function setDevelopDomainList(array $developDomainList){
		$this->developDomainList = $developDomainList;
	}
	
}
