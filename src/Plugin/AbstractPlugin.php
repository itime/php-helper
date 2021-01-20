<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Plugin;

use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Contracts\Plugin\Plugin as PluginContract;

abstract class AbstractPlugin implements PluginContract{
	
	/**
	 * @var array
	 */
	protected $configTemplate = null;
	
	/**
	 * @var PluginFactory
	 */
	protected $factory;
	
	/**
	 * AbstractPlugin constructor.
	 *
	 * @param \Xin\Contracts\Plugin\Factory $factory
	 */
	public function __construct(PluginFactory $factory){ $this->factory = $factory; }
	
	/**
	 * @inheritDoc
	 */
	public function getName(){
		return $this->getInfo()['name'];
	}
	
	/**
	 * @inheritDoc
	 */
	public function getConfigTemplate($config = []){
		$template = $this->loadConfigTemplate();
		
		foreach($template as &$item){
			foreach($item['config'] as &$value){
				$name = $value['name'];
				if(isset($config[$name])){
					$value['value'] = $config[$name];
				}else{
					$value['value'] = value(isset($value['value']) ? $value['value'] : null);
				}
			}
			unset($value);
		}
		unset($item);
		
		return $template;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getConfigTypeList(){
		$template = $this->loadConfigTemplate();
		
		$typeMap = [
			'switch' => 'int',
			'number' => 'int',
			'array'  => 'array',
		];
		
		$result = [];
		foreach($template as $item){
			foreach($item['config'] as $value){
				if(isset($value['typeof'])){
					$result[$value['name']] = $value['typeof'];
				}else{
					if(isset($typeMap[$value['type']])){
						$result[$value['name']] = $typeMap[$value['type']];
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * 解析值
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	protected function resolveValue($value){
		return $value instanceof \Closure ? $value() : $value;
	}
	
	/**
	 * 加载配置信息模板
	 *
	 * @return array
	 * @noinspection PhpIncludeInspection
	 */
	protected function loadConfigTemplate(){
		if(is_null($this->configTemplate)){
			$configTemplatePath = $this->pluginPath()."config.php";
			if(file_exists($configTemplatePath)){
				$this->configTemplate = require_once $configTemplatePath;
			}else{
				$this->configTemplate = [];
			}
		}
		
		return $this->configTemplate;
	}
	
	/**
	 * @inheritDoc
	 */
	public function install(){
	}
	
	/**
	 * @inheritDoc
	 */
	public function uninstall(){
	}
	
	/**
	 * @inheritDoc
	 */
	public function boot(){
	}
	
	/**
	 * @inheritDoc
	 */
	public function pluginPath($path = ''){
		$rootPath = $this->factory->path($this->getName());
		return $rootPath.($path ? $path.DIRECTORY_SEPARATOR : $path);
	}
	
}
