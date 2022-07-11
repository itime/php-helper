<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Support;

use Doctrine\Inflector\InflectorFactory;

class Pluralizer
{

	/**
	 * Uncountable word forms.
	 *
	 * @var array
	 */
	public static $uncountable = [
		'audio',
		'bison',
		'cattle',
		'chassis',
		'compensation',
		'coreopsis',
		'data',
		'deer',
		'education',
		'emoji',
		'equipment',
		'evidence',
		'feedback',
		'firmware',
		'fish',
		'furniture',
		'gold',
		'hardware',
		'information',
		'jedi',
		'kin',
		'knowledge',
		'love',
		'metadata',
		'money',
		'moose',
		'news',
		'nutrition',
		'offspring',
		'plankton',
		'pokemon',
		'police',
		'rain',
		'recommended',
		'related',
		'rice',
		'series',
		'sheep',
		'software',
		'species',
		'swine',
		'traffic',
		'wheat',
	];

	/**
	 * @var \Doctrine\Inflector\Inflector
	 */
	protected static $inflector = null;

	/**
	 * @return \Doctrine\Inflector\Inflector
	 */
	public static function inflector()
	{
		if (static::$inflector === null) {
			static::$inflector = InflectorFactory::create()->build();
		}

		return static::$inflector;
	}

	/**
	 * Get the plural form of an English word.
	 *
	 * @param string $value
	 * @param int $count
	 * @return string
	 */
	public static function plural($value, $count = 2)
	{
		if ((int)abs($count) === 1 || static::uncountable($value)) {
			return $value;
		}

		$plural = static::inflector()->pluralize($value);

		return static::matchCase($plural, $value);
	}

	/**
	 * Get the singular form of an English word.
	 *
	 * @param string $value
	 * @return string
	 */
	public static function singular($value)
	{
		$singular = static::inflector()->singularize($value);

		return static::matchCase($singular, $value);
	}

	/**
	 * Determine if the given value is uncountable.
	 *
	 * @param string $value
	 * @return bool
	 */
	protected static function uncountable($value)
	{
		return in_array(strtolower($value), static::$uncountable);
	}

	/**
	 * Attempt to match the case on two strings.
	 *
	 * @param string $value
	 * @param string $comparison
	 * @return string
	 */
	protected static function matchCase($value, $comparison)
	{
		$functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];

		foreach ($functions as $function) {
			if (call_user_func($function, $comparison) === $comparison) {
				return call_user_func($function, $value);
			}
		}

		return $value;
	}

}
