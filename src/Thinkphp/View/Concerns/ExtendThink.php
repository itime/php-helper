<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\View\Concerns;

trait ExtendThink{

	protected function extendThink(){
		$this->extend('$Think', function(array $vars){
			$type = strtoupper(trim(array_shift($vars)));
			$param = implode('.', $vars);

			switch($type){
				case 'CONST':
					$parseStr = strtoupper($param);
					break;
				case 'CONFIG':
					$parseStr = 'config(\''.$param.'\')';
					break;
				case 'LANG':
					$parseStr = 'lang(\''.$param.'\')';
					break;
				case 'NOW':
					$parseStr = "date('Y-m-d g:i a',time())";
					break;
				case 'LDELIM':
					$parseStr = '\''.ltrim($this->getConfig('tpl_begin'), '\\').'\'';
					break;
				case 'RDELIM':
					$parseStr = '\''.ltrim($this->getConfig('tpl_end'), '\\').'\'';
					break;
				default:
					$parseStr = defined($type) ? $type : '\'\'';
			}

			return $parseStr;
		});
	}

	protected function extendRequest(){
		$this->extend('$Request', function(array $vars){
			// 获取Request请求对象参数
			$method = array_shift($vars);
			if(!empty($vars)){
				$params = implode('.', $vars);
				if('true' != $params){
					$params = '\''.$params.'\'';
				}
			}else{
				$params = '';
			}

			return 'app(\'request\')->'.$method.'('.$params.')';
		});
	}
}
