<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Setting\Command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Xin\Thinkphp\Setting\Setting;

class Show extends Command{
	
	/**
	 * @inheritDoc
	 */
	protected function configure(){
		$this->setName('setting:show')
			->setDescription('查看站点配置');
	}
	
	/**
	 * @param \think\console\Input  $input
	 * @param \think\console\Output $output
	 */
	protected function execute(Input $input, Output $output){
		$output->highlight(
			json_encode(Setting::load(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
		);
		$output->newLine();
	}
}
