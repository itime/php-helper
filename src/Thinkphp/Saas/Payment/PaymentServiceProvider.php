<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Payment;

use Xin\Contracts\Foundation\Payment as PaymentContract;
use Xin\Thinkphp\Foundation\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider{
	
	/**
	 * 启动器
	 */
	public function register(){
		$this->app->bind('payment', PaymentContract::class);
		$this->app->bind(PaymentContract::class, function(){
			return new Payment(
				$this->app->config->get('payment')
			);
		});
	}
}
