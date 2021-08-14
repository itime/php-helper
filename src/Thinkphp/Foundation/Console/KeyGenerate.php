<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Console;

use think\console\input\Option;
use Xin\Thinkphp\Console\Command;
use Xin\Thinkphp\Console\ConfirmableTrait;

class KeyGenerate extends Command{

	use ConfirmableTrait;

	/**
	 * 配置命令
	 */
	protected function configure(){
		$this->setName('key:generate')
			->addOption('show', null, Option::VALUE_NONE, "Display the key instead of modifying files")
			->addOption('force', null, Option::VALUE_NONE, 'Force the operation to run when in production')
			->setDescription('Set the application key');
	}

	/**
	 * Execute the console command.
	 *
	 * @throws \Exception
	 */
	public function handle(){
		$key = $this->generateRandomKey();

		if($this->input->hasOption('show')){
			$this->output->comment($key);
			return;
		}

		// Next, we will replace the application key in the environment file so it is
		// automatically setup for this developer. This key gets generated using a
		// secure random byte generator and is later base64 encoded for storage.
		if(!$this->setKeyInEnvironmentFile($key)){
			return;
		}

		$this->getApp()->config->set([
			'key' => $key,
		], 'app');

		$this->output->comment("generate key: ".$key);
		$this->output->info('Application key set successfully.');
	}

	/**
	 * Generate a random key for the application.
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function generateRandomKey(){
		return 'base64:'.base64_encode(random_bytes(32));
	}

	/**
	 * Set the application key in the environment file.
	 *
	 * @param string $key
	 * @return bool
	 */
	protected function setKeyInEnvironmentFile($key){
		$currentKey = $this->getApp()->config->get('app.key');

		if(strlen($currentKey) !== 0 && (!$this->confirmToProceed())){
			return false;
		}

		$this->writeNewEnvironmentFileWith($key);

		return true;
	}

	/**
	 * Write a new environment file with the given key.
	 *
	 * @param string $key
	 * @return void
	 */
	protected function writeNewEnvironmentFileWith($key){
		$environmentFilePath = $this->getApp()->getRootPath().".env";
		file_put_contents($environmentFilePath, preg_replace(
			$this->keyReplacementPattern(),
			"APP_KEY = \"{$key}\"",
			file_get_contents($environmentFilePath)
		));
	}

	/**
	 * Get a regex pattern that will match env APP_KEY with any random key.
	 *
	 * @return string
	 */
	protected function keyReplacementPattern(){
		$escaped = preg_quote(' = "'.$this->getApp()->config->get('app.key').'"', '/');

		return "/^APP_KEY{$escaped}/m";
	}
}
