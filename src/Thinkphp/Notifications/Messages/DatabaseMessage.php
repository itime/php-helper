<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Thinkphp\Notifications\Messages;

class DatabaseMessage{

	/**
	 * The data that should be stored with the notification.
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * Create a new database message.
	 *
	 * @param array $data
	 * @return void
	 */
	public function __construct(array $data = []){
		$this->data = $data;
	}
}
