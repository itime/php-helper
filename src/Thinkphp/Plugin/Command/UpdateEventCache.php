<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin\Command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Xin\Thinkphp\Plugin\DatabaseEvent;

class UpdateEventCache extends Command
{

	/**
	 * @inheritDoc
	 */
	protected function configure()
	{
		$this->setName('event:refresh')
			->setDescription('刷新事件缓存配置');
	}

	/**
	 * @param \think\console\Input $input
	 * @param \think\console\Output $output
	 */
	protected function execute(Input $input, Output $output)
	{
		DatabaseEvent::refreshCache();

		$output->highlight("已更新缓存配置！");
	}

}
