<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Console;

/**
 * Trait ConfirmableTrait
 *
 * @mixin \think\console\Command
 */
trait ConfirmableTrait
{

	/**
	 * Confirm before proceeding with the action.
	 * This method only asks for confirmation in production.
	 *
	 * @param string $warning
	 * @param \Closure|bool|null $callback
	 * @return bool
	 */
	public function confirmToProceed($warning = 'Application In Production!', $callback = null)
	{
		$callback = is_null($callback) ? $this->getDefaultConfirmCallback() : $callback;

		$shouldConfirm = value($callback);

		if ($shouldConfirm) {
			if ($this->input->hasOption('force') && $this->input->getOption('force')) {
				return true;
			}

			$this->output->warning($warning);

			$confirmed = $this->output->confirm($this->input, 'Do you really wish to run this command?');

			if (!$confirmed) {
				$this->output->comment('Command Canceled!');

				return false;
			}
		}

		return true;
	}

	/**
	 * Get the default confirmation callback.
	 *
	 * @return \Closure
	 */
	protected function getDefaultConfirmCallback()
	{
		return function () {
			return $this->getApp()->config->get('app.env') === 'production';
		};
	}

}
