<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Foundation\Setting;

use think\facade\Config;
use think\Validate;

/**
 * 配置验证器
 */
class SettingValidate extends Validate{

	/**
	 * 验证规则
	 *
	 * @var array
	 */
	protected $rule = [
		'name'  => 'require|regex:/^[A-Za-z0-9\-\_\.]+$/|length:3,32|unique:setting',
		'title' => 'require|length:2,12',
		'group' => 'alphaDash',
		'type'  => 'alphaDash',
	];

	/**
	 * 字段信息
	 *
	 * @var array
	 */
	protected $field = [
		'name'  => '配置标识',
		'title' => '配置标题',
		'group' => '配置分组',
		'type'  => '配置类型',
	];

	/**
	 * 情景模式
	 *
	 * @var array
	 */
	protected $scene = [];

	/**
	 * SettingValidate constructor.
	 */
	public function __construct(){
		$typeList = Config::get('web.config_type_list');
		if(!empty($typeList)){
			$this->rule['type'] .= "|in:".implode(",", array_keys($typeList));
		}

		$groupList = Config::get('web.config_group_list');
		if(!empty($groupList)){
			$this->rule['group'] .= "|in:".implode(",", array_keys($groupList));
		}

		parent::__construct();
	}
}
