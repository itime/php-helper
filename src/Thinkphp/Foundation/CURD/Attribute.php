<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\CURD;

use think\exception\ValidateException;
use think\facade\Db;
use Xin\Support\Str;

trait Attribute {

	/**
	 * 获取模型实例
	 *
	 * @param array $data
	 * @return \think\db\BaseQuery|\think\Model
	 */
	protected function model($data = []) {
		$model = $this->property('model', $this->getClassName());

		if (is_string($model)) {
			$model = $this->resolveCommonClass($model, 'model', false);
		} elseif (is_object($model)) {
			$model = get_class($model);
		}

		if ($model) {
			return new $model($data);
		}

		return Db::instance()->name($this->getClassName())->data($data);
	}

	/**
	 * 获取验证器路径
	 *
	 * @param string $scene
	 * @return \think\Validate
	 */
	protected function validator($scene) {
		$validator = $this->property('validator', $this->getClassName());

		$validator = $this->resolveCommonClass($validator, 'validate');
		if (!$validator) {
			return null;
		}

		/** @var \think\Validate $validator */
		$validator = app($validator);
		$validator->scene($scene);

		return $validator;
	}

	/**
	 * 验证数据合法性
	 *
	 * @param array  $data
	 * @param string $scene
	 * @return array
	 */
	protected function validateData($data, $scene) {
		$validator = $this->validator($scene);
		if (!$validator) {
			return $data;
		}

		if (!$validator->check($data)) {
			throw new ValidateException($validator->getError());
		}

		return $data;
	}

	/**
	 * 调用类方法
	 *
	 * @param string $method
	 * @param array  $vars
	 * @return mixed
	 */
	protected function invokeMethod($method, $vars = []) {
		if (!method_exists($this, $method)) {
			return null;
		}

		return app()->invoke([$this, $method], $vars, true);
	}

	/**
	 * 获取 class name
	 *
	 * @return string
	 */
	protected function getClassName() {
		$class = explode('\\', get_class($this));
		$class = end($class);
		$class = substr($class, 0, strpos($class, "Controller"));

		return $class;
	}

	/**
	 * 获取属性
	 *
	 * @param string $property
	 * @param mixed  $default
	 * @return mixed
	 */
	protected function property($property, $default = null) {
		if (property_exists($this, $property)) {
			return $this->{$property};
		}

		return $default;
	}

	/**
	 * 解析公共类库下的类
	 *
	 * @param string $baseClass
	 * @param string $layer
	 * @param bool   $appendSuffix
	 * @return string|null
	 */
	private function resolveCommonClass($baseClass, $layer, $appendSuffix = true) {
		if (strpos($baseClass, "\\") !== false) {
			return $baseClass;
		}

		$baseClass = $baseClass . ($appendSuffix ? Str::studly($layer) : '');
		$class = "\\app\\common\\{$layer}\\{$baseClass}";

		if (!class_exists($class)) {
			return null;
		}

		return $class;
	}

}
