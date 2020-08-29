<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Xin\Thinkphp\Plugin\Facade\Plugin;

trait PluginView{
	
	protected function initialize(){
		dump('1');
		parent::initialize();
		dump('2');
		$this->view->engine()->config([
			"view_dir_name" => "view",
			"view_path"     => Plugin::path("mall".DIRECTORY_SEPARATOR."adminview"),
		]);
	}
}
