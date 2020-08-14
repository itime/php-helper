<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

class MenuManager{

	/**
	 * @var array
	 */
	protected $menus = [];

	public function __construct($menus){
		$this->menus = $menus;
	}

	public function genator(){
	}
}
