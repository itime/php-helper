<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas;

use Xin\Contracts\Saas\WechatRepository;
use Xin\Foundation\Wechat as WechatBase;

class Wechat extends WechatBase implements WechatRepository{
	
	/**
	 * @inheritDoc
	 */
	public function openPlatformOfId($id, array $options = []){
		// TODO: Implement openPlatformOfId() method.
		if($id == 0){
			return $this->defaultOpenPlatform($options);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function openPlatformOfAppId($appId, array $options = []){
		// TODO: Implement openPlatformOfAppId() method.
		if($appId == 0){
			return $this->defaultOpenPlatform($options);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function officialOfId($id, array $options = []){
		// TODO: Implement officialOfId() method.
		if($id == 0){
			return $this->defaultOfficial($options);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function officialOfAppId($appId, array $options = []){
		// TODO: Implement officialOfAppId() method.
		if($appId == 0){
			return $this->defaultOfficial($options);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function miniProgramOfId($id, array $options = []){
		// TODO: Implement miniProgramOfId() method.
		if($id == 0){
			return $this->defaultMiniProgram($options);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function miniProgramOfAppId($appId, array $options = []){
		// TODO: Implement miniProgramOfAppId() method.
		if($appId == 0){
			return $this->defaultMiniProgram($options);
		}
	}
}
