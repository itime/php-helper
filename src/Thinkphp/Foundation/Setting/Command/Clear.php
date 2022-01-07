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
use think\facade\Cache;
use Xin\Thinkphp\Foundation\Setting\DatabaseSetting;

class Clear extends Command {

	/**
	 * @inheritDoc
	 */
	protected function configure() {
		$this->setName('setting:clear')
			->setDescription('清除站点配置');
	}

	/**
	 * @param \think\console\Input  $input
	 * @param \think\console\Output $output
	 */
	protected function execute(Input $input, Output $output) {
		Cache::delete(DatabaseSetting::CACHE_KEY);

		$output->highlight("已更新配置！");
		$output->newLine();
	}

}
