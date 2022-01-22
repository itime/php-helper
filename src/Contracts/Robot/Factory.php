<?php

namespace Xin\Contracts\Robot;

interface Factory {

	/**
	 * 选择机器人
	 * @param string $name
	 * @return Robot
	 */
	public function robot($name = null);

}
