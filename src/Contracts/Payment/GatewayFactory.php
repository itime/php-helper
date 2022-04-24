<?php

namespace Xin\Contracts\Payment;

interface GatewayFactory
{
	/**
	 * @param string $type
	 * @return Gateway
	 */
	public function pay($type = null);
}