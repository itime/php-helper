<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Access;

use Psr\Container\ContainerInterface;
use Xin\Contracts\Auth\Access\Gate as GateContract;
use Xin\Support\Arr;
use Xin\Support\Str;

class Gate implements GateContract{

	/**
	 * 容器实例
	 *
	 * @var \Psr\Container\ContainerInterface
	 */
	protected $container;

	/**
	 * 用户解析解决器
	 *
	 * @var callable
	 */
	protected $userResolver;

	/**
	 * 所有定义的能力
	 *
	 * @var array
	 */
	protected $abilities = [];

	/**
	 * 所有定义的策略
	 *
	 * @var array
	 */
	protected $policies = [];

	/**
	 * 所有在回调之前注册的
	 *
	 * @var array
	 */
	protected $beforeCallbacks = [];

	/**
	 * 所有在回调后注册的
	 *
	 * @var array
	 */
	protected $afterCallbacks = [];

	/**
	 * 所有定义的能力，使用class@method notation.
	 *
	 * @var array
	 */
	protected $stringCallbacks = [];

	/**
	 * 策略名称探测器
	 *
	 * @var callable|null
	 */
	protected $guessPolicyNamesUsingCallback;

	/**
	 * Create a new gate instance.
	 *
	 * @param ContainerInterface $container
	 * @param callable           $userResolver
	 * @param array              $abilities
	 * @param array              $policies
	 * @param array              $beforeCallbacks
	 * @param array              $afterCallbacks
	 * @param callable|null      $guessPolicyNamesUsingCallback
	 * @return void
	 */
	public function __construct(ContainerInterface $container, callable $userResolver, array $abilities = [],
		array $policies = [], array $beforeCallbacks = [], array $afterCallbacks = [],
		callable $guessPolicyNamesUsingCallback = null){
		$this->container = $container;
		$this->userResolver = $userResolver;
		$this->abilities = $abilities;
		$this->policies = $policies;
		$this->afterCallbacks = $afterCallbacks;
		$this->beforeCallbacks = $beforeCallbacks;
		$this->guessPolicyNamesUsingCallback = $guessPolicyNamesUsingCallback;
	}

	/**
	 *给定的能力是否已定义
	 *
	 * @param string|array $ability
	 * @return bool
	 */
	public function has($ability){
		$abilities = is_array($ability) ? $ability : func_get_args();

		foreach($abilities as $ability){
			if(!isset($this->abilities[$ability])){
				return false;
			}
		}

		return true;
	}

	/**
	 * 定义一个新的能力
	 *
	 * @param string          $ability
	 * @param callable|string $callback
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function define($ability, $callback){
		if(is_callable($callback)){
			$this->abilities[$ability] = $callback;
		}elseif(is_string($callback)){
			$this->stringCallbacks[$ability] = $callback;
			$this->abilities[$ability] = $this->buildAbilityCallback($ability, $callback);
		}else{
			throw new \InvalidArgumentException("Callback must be a callable or a 'Class@method' string.");
		}

		return $this;
	}

	/**
	 * 定义资源的能力
	 *
	 * @param string     $name
	 * @param string     $class
	 * @param array|null $abilities
	 * @return $this
	 */
	public function resource($name, $class, array $abilities = null){
		$abilities = $abilities
			?: [
				'viewAny' => 'viewAny',
				'view'    => 'view',
				'create'  => 'create',
				'update'  => 'update',
				'delete'  => 'delete',
			];

		foreach($abilities as $ability => $method){
			$this->define($name.'.'.$ability, $class.'@'.$method);
		}

		return $this;
	}

	/**
	 * 为回调字符串创建能力回调
	 *
	 * @param string $ability
	 * @param string $callback
	 * @return \Closure
	 */
	protected function buildAbilityCallback($ability, $callback){
		return function() use ($ability, $callback){
			if(Str::contains($callback, '@')){
				[$class, $method] = self::parseCallback($callback);
			}else{
				$class = $callback;
			}

			$policy = $this->resolvePolicy($class);

			$arguments = func_get_args();

			$user = array_shift($arguments);

			$result = $this->callPolicyBefore(
				$policy, $user, $ability, $arguments
			);

			if(!is_null($result)){
				return $result;
			}

			return isset($method)
				? $policy->{$method}(...func_get_args())
				: $policy(...func_get_args());
		};
	}

	/**
	 * 为给定的类类型定义策略类
	 *
	 * @param string $class
	 * @param string $policy
	 * @return $this
	 */
	public function policy($class, $policy){
		$this->policies[$class] = $policy;

		return $this;
	}

	/**
	 * 注册一个回调以在所有检查之前运行
	 *
	 * @param callable $callback
	 * @return $this
	 */
	public function before(callable $callback){
		$this->beforeCallbacks[] = $callback;

		return $this;
	}

	/**
	 * 注册一个回调以在所有检查之后运行
	 *
	 * @param callable $callback
	 * @return $this
	 */
	public function after(callable $callback){
		$this->afterCallbacks[] = $callback;

		return $this;
	}

	/**
	 * 是否应拒绝当前用户的给定能力
	 *
	 * @param string      $ability
	 * @param array|mixed $arguments
	 * @return bool
	 */
	public function denies($ability, $arguments = []){
		return !$this->check($ability, $arguments);
	}

	/**
	 * 是否应该为当前用户授予所有给定的能力
	 *
	 * @param iterable|string $abilities
	 * @param array|mixed     $arguments
	 * @return bool
	 */
	public function check($abilities, $arguments = []){
		return Arr::every(Arr::wrap($abilities), function($ability) use ($arguments){
			try{
				return (bool)$this->raw($ability, $arguments);
			}catch(AuthorizationException $e){
				return false;
			}
		});
	}

	/**
	 * 是否应为当前用户授予任何给定的能力
	 *
	 * @param array|iterable $abilities
	 * @param array|mixed    $arguments
	 * @return bool
	 */
	public function any($abilities, $arguments = []){
		return Arr::first($abilities, function($ability) use ($arguments){
			return $this->check($ability, $arguments);
		});
	}

	/**
	 * 从授权回调获取原始结果
	 *
	 * @param string      $ability
	 * @param array|mixed $arguments
	 * @return mixed
	 */
	public function raw($ability, $arguments = []){
		$arguments = Arr::wrap($arguments);

		$user = $this->resolveUser();

		// First we will call the "before" callbacks for the Gate. If any of these give
		// back a non-null response, we will immediately return that result in order
		// to let the developers override all checks for some authorization cases.
		$result = $this->callBeforeCallbacks(
			$user, $ability, $arguments
		);

		if(is_null($result)){
			$result = $this->callAuthCallback($user, $ability, $arguments);
		}

		// After calling the authorization callback, we will call the "after" callbacks
		// that are registered with the Gate, which allows a developer to do logging
		// if that is required for this application. Then we'll return the result.
		return $this->callAfterCallbacks(
			$user, $ability, $arguments, $result
		);
	}

	/**
	 * 是否可以使用给定的回调/方法
	 *
	 * @param mixed                 $user
	 * @param \Closure|string|array $class
	 * @param string|null           $method
	 * @return bool
	 */
	protected function canBeCalledWithUser($user, $class, $method = null){
		if(!is_null($user)){
			return true;
		}

		if(!is_null($method)){
			return $this->methodAllowsGuests($class, $method);
		}

		if(is_array($class)){
			$className = is_string($class[0]) ? $class[0] : get_class($class[0]);

			return $this->methodAllowsGuests($className, $class[1]);
		}

		return $this->callbackAllowsGuests($class);
	}

	/**
	 * 给定的类方法是否允许来宾访问
	 *
	 * @param string $class
	 * @param string $method
	 * @return bool
	 */
	protected function methodAllowsGuests($class, $method){
		try{
			$reflection = new \ReflectionClass($class);
			$method = $reflection->getMethod($method);
		}catch(\Exception $e){
			return false;
		}

		if($method){
			$parameters = $method->getParameters();
			return isset($parameters[0]) && $this->parameterAllowsGuests($parameters[0]);
		}

		return false;
	}

	/**
	 * 回调是否允许来宾访问
	 *
	 * @param callable $callback
	 * @return bool
	 * @noinspection PhpDocMissingThrowsInspection
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	protected function callbackAllowsGuests($callback){
		$parameters = (new \ReflectionFunction($callback))->getParameters();

		return isset($parameters[0]) && $this->parameterAllowsGuests($parameters[0]);
	}

	/**
	 * 给定参数是否允许来宾访问
	 *
	 * @param \ReflectionParameter $parameter
	 * @return bool
	 * @noinspection PhpDocMissingThrowsInspection
	 * @noinspection PhpUnhandledExceptionInspection
	 */
	protected function parameterAllowsGuests($parameter){
		return ($parameter->getClass() && $parameter->allowsNull())
			|| ($parameter->isDefaultValueAvailable() && is_null($parameter->getDefaultValue()));
	}

	/**
	 * 解决授权用户回调
	 *
	 * @param mixed  $user
	 * @param string $ability
	 * @param array  $arguments
	 * @return bool
	 */
	protected function callAuthCallback($user, $ability, array $arguments){
		$callback = $this->resolveAuthCallback($user, $ability, $arguments);

		return $callback($user, ...$arguments);
	}

	/**
	 * 调用所有before回调并在给定结果时返回
	 *
	 * @param mixed  $user
	 * @param string $ability
	 * @param array  $arguments
	 * @return bool|void
	 */
	protected function callBeforeCallbacks($user, $ability, array $arguments){
		foreach($this->beforeCallbacks as $before){
			if(!$this->canBeCalledWithUser($user, $before)){
				continue;
			}

			if(!is_null($result = $before($user, $ability, $arguments))){
				return $result;
			}
		}
	}

	/**
	 * 检查结果调用所有after回调
	 *
	 * @param mixed  $user
	 * @param string $ability
	 * @param array  $arguments
	 * @param bool   $result
	 * @return bool|null
	 */
	protected function callAfterCallbacks($user, $ability, array $arguments, $result){
		foreach($this->afterCallbacks as $after){
			if(!$this->canBeCalledWithUser($user, $after)){
				continue;
			}

			$afterResult = $after($user, $ability, $result, $arguments);

			$result = $result ?? $afterResult;
		}

		return $result;
	}

	/**
	 * 解决给定的能力和参数解析回调
	 *
	 * @param mixed  $user
	 * @param string $ability
	 * @param array  $arguments
	 * @return callable
	 */
	protected function resolveAuthCallback($user, $ability, array $arguments){
		if(isset($arguments[0])
			&& !is_null($policy = $this->getPolicyFor($arguments[0]))
			&& $callback = $this->resolvePolicyCallback($user, $ability, $arguments, $policy)){
			return $callback;
		}

		if(isset($this->stringCallbacks[$ability])){
			[$class, $method] = static::parseCallback($this->stringCallbacks[$ability]);

			if($this->canBeCalledWithUser($user, $class, $method ?: '__invoke')){
				return $this->abilities[$ability];
			}
		}

		if(isset($this->abilities[$ability])
			&& $this->canBeCalledWithUser($user, $this->abilities[$ability])){
			return $this->abilities[$ability];
		}

		return function(){
		};
	}

	/**
	 * 获取给定类的策略实例
	 *
	 * @param object|string $class
	 * @return mixed|void
	 */
	public function getPolicyFor($class){
		if(is_object($class)){
			$class = get_class($class);
		}

		if(!is_string($class)){
			return null;
		}

		if(isset($this->policies[$class])){
			return $this->resolvePolicy($this->policies[$class]);
		}

		foreach($this->guessPolicyName($class) as $guessedPolicy){
			if(class_exists($guessedPolicy)){
				return $this->resolvePolicy($guessedPolicy);
			}
		}

		foreach($this->policies as $expected => $policy){
			if(is_subclass_of($class, $expected)){
				return $this->resolvePolicy($policy);
			}
		}
	}

	/**
	 * 猜测给定类的策略名称
	 *
	 * @param string $class
	 * @return array
	 */
	protected function guessPolicyName($class){
		if($this->guessPolicyNamesUsingCallback){
			return Arr::wrap(call_user_func($this->guessPolicyNamesUsingCallback, $class));
		}

		$classDirname = str_replace('/', '\\', dirname(str_replace('\\', '/', $class)));

		return [$classDirname.'\\policies\\'.class_basename($class).'Policy'];
	}

	/**
	 * 指定用于猜测策略名称的回调
	 *
	 * @param callable $callback
	 * @return $this
	 */
	public function guessPolicyNamesUsing(callable $callback){
		$this->guessPolicyNamesUsingCallback = $callback;

		return $this;
	}

	/**
	 * Build a policy class instance of the given type.
	 *
	 * @param object|string $class
	 * @return mixed
	 */
	public function resolvePolicy($class){
		return $this->container->get($class);
	}

	/**
	 * Resolve the callback for a policy check.
	 *
	 * @param mixed  $user
	 * @param string $ability
	 * @param array  $arguments
	 * @param mixed  $policy
	 * @return bool|callable
	 */
	protected function resolvePolicyCallback($user, $ability, array $arguments, $policy){
		if(!is_callable([$policy, $this->formatAbilityToMethod($ability)])){
			return false;
		}

		return function() use ($user, $ability, $arguments, $policy){
			// This callback will be responsible for calling the policy's before method and
			// running this policy method if necessary. This is used to when objects are
			// mapped to policy objects in the user's configurations or on this class.
			$result = $this->callPolicyBefore(
				$policy, $user, $ability, $arguments
			);

			// When we receive a non-null result from this before method, we will return it
			// as the "final" results. This will allow developers to override the checks
			// in this policy to return the result for all rules defined in the class.
			if(!is_null($result)){
				return $result;
			}

			$method = $this->formatAbilityToMethod($ability);

			return $this->callPolicyMethod($policy, $method, $user, $arguments);
		};
	}

	/**
	 * 如果适用，请对给定策略调用“before”方法
	 *
	 * @param mixed  $policy
	 * @param mixed  $user
	 * @param string $ability
	 * @param array  $arguments
	 * @return mixed|void
	 */
	protected function callPolicyBefore($policy, $user, $ability, $arguments){
		if(!method_exists($policy, 'before')){
			return null;
		}

		if($this->canBeCalledWithUser($user, $policy, 'before')){
			return $policy->before($user, $ability, ...$arguments);
		}
	}

	/**
	 * Call the appropriate method on the given policy.
	 *
	 * @param mixed  $policy
	 * @param string $method
	 * @param mixed  $user
	 * @param array  $arguments
	 * @return mixed
	 */
	protected function callPolicyMethod($policy, $method, $user, array $arguments){
		// If this first argument is a string, that means they are passing a class name
		// to the policy. We will remove the first argument from this argument array
		// because this policy already knows what type of models it can authorize.
		if(isset($arguments[0]) && is_string($arguments[0])){
			array_shift($arguments);
		}

		if(!is_callable([$policy, $method])){
			return null;
		}

		if($this->canBeCalledWithUser($user, $policy, $method)){
			return $policy->{$method}($user, ...$arguments);
		}

		return null;
	}

	/**
	 * 将策略功能格式化为方法名
	 *
	 * @param string $ability
	 * @return string
	 */
	protected function formatAbilityToMethod($ability){
		return strpos($ability, '-') !== false ? Str::camel($ability) : $ability;
	}

	/**
	 * 获取给定用户的gate实例
	 *
	 * @param mixed $user
	 * @return static
	 */
	public function forUser($user){
		$callback = function() use ($user){
			return $user;
		};

		return new static(
			$this->container, $callback, $this->abilities,
			$this->policies, $this->beforeCallbacks, $this->afterCallbacks,
			$this->guessPolicyNamesUsingCallback
		);
	}

	/**
	 * 从用户解析程序解析用户
	 *
	 * @return mixed
	 */
	protected function resolveUser(){
		return call_user_func($this->userResolver);
	}

	/**
	 * 获得所有定义的能力
	 *
	 * @return array
	 */
	public function abilities(){
		return $this->abilities;
	}

	/**
	 * 获取所有已定义的策略
	 *
	 * @return array
	 */
	public function policies(){
		return $this->policies;
	}

	/**
	 * @param string      $callback
	 * @param string|null $default
	 * @return array
	 */
	public static function parseCallback($callback, $default = null){
		return Str::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
	}
}
