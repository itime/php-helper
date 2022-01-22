<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Validate;

use think\Validate;
use Xin\Thinkphp\Foundation\ServiceProvider;

class ValidateServiceProvider extends ServiceProvider {

	/**
	 * @inheritDoc
	 */
	public function register() {
		Validate::maker(function (Validate $v) {
			$v->extend('alphaDash2', function ($value) use ($v) {
				return $v->regex($value, '/^[A-Za-z0-9\.\-\_\\\]+$/');
			}, ':attribute只能是字母、数字和下划线_破折号-反斜杠\\');

			$v->extend('chsDash2', function ($value) use ($v) {
				return $v->regex($value, '/^[A-Za-z0-9\-\_\\\]+$/');
			}, ':attribute只能是汉字、字母、数字和下划线_破折号-反斜杠\\');

			$v->extend('phone', function ($value) use ($v) {
				return $v->regex($value, '/^1\d{10}+$/');
			}, ':attribute不是一个合法的手机号');

			$v->extend('password', function ($value) use ($v) {
				return $v->regex($value, '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{6,16}/');
			}, ':attribute无效');
		});
	}

}
