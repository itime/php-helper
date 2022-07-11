<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Setting\Command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Xin\Thinkphp\Foundation\Setting\DatabaseSetting;

class Update extends Command
{

	/**
	 * @inheritDoc
	 */
	protected function configure()
	{
		$this->setName('setting:update')
			->setDescription('刷新站点配置');
	}

	/**
	 * @param \think\console\Input $input
	 * @param \think\console\Output $output
	 */
	protected function execute(Input $input, Output $output)
	{
		DatabaseSetting::refreshCache();

		$output->highlight("已更新配置！");
	}

}
