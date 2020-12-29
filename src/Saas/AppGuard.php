<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Saas;

use Xin\Contracts\Saas\AppGuard as AppGuardContract;

class AppGuard implements AppGuardContract{
	
	public function getAppInfo($field = null, $default = null){
		// TODO: Implement getAppInfo() method.
	}
	
	public function getAppId(){
		// TODO: Implement getAppId() method.
	}
	
	public function temporaryAppInfo($info){
		// TODO: Implement temporaryAppInfo() method.
	}
	
	public function getConfig(){
		// TODO: Implement getConfig() method.
	}
}
