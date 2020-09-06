<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\CURD;

trait InteractsCURD{
	
	/**
	 * @var \Xin\Thinkphp\Foundation\CURD\CURD
	 */
	protected $curd = null;
	
	/**
	 * @param string $name
	 * @param array  $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments){
		return call_user_func_array([
			$this->curd(),
			$name,
		], $arguments);
	}
	
	/**
	 * @return \Xin\Thinkphp\Foundation\CURD\CURD
	 */
	protected function curd(){
		if($this->curd === null){
			$this->curd = new CURD(app(), static::class);
		}
		
		return $this->curd;
	}
}
