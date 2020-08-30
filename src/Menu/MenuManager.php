<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Support\Manager;

/**
 * Class MenuManager
 * @method \Xin\Contracts\Menu\Menu driver($driver = null)
 */
class MenuManager extends Manager{
	
	/**
	 * @inheritDoc
	 */
	public function getDefaultDriver(){
		return "array";
	}
	
	/**
	 * @return \Xin\Menu\ArrayMenu
	 */
	public function createArrayDriver(){
		return new ArrayMenu();
	}
	
	/**
	 * @param array $options
	 * @return array
	 */
	public function generate(array $options = []){
		return $this->driver()->generate($options);
	}
}
