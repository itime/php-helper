<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Facade;

use think\Facade;
use Xin\Contracts\Auth\Access\Gate as GateContract;

/**
 * @method static bool has(string $ability)
 * @method static GateContract define(string $ability, callable|string $callback)
 * @method static GateContract policy(string $class, string $policy)
 * @method static GateContract before(callable $callback)
 * @method static GateContract after(callable $callback)
 * @method static bool allows(string $ability, array|mixed $arguments = [])
 * @method static bool denies(string $ability, array|mixed $arguments = [])
 * @method static bool check(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool any(iterable|string $abilities, array|mixed $arguments = [])
 * @method static mixed raw(string $ability, array|mixed $arguments = [])
 * @method static mixed getPolicyFor(object|string $class)
 * @method static GateContract forUser(mixed $user)
 * @method static array abilities()
 * @see \Xin\Contracts\Auth\Access\Gate
 */
class Gate extends Facade
{

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeClass()
	{
		return GateContract::class;
	}

}
