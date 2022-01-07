<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Facade;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use think\Facade;
use think\facade\Config;

/**
 * @method static string name(string $gender = null)
 * @method static string firstName(string $gender = null)
 * @method static string title(string $gender = null)
 * @method static string creditCardNumber($type = null, $formatted = false, $separator = '-')
 * @method static string iban($countryCode = null, $prefix = '', $length = null)
 * @method static string|array words($nb = 3, $asText = false)
 * @method static string word()
 * @method static string sentence($nbWords = 6, $variableNbWords = true)
 * @method static string|array sentences($nb = 3, $asText = false)
 * @method static string paragraph($nbSentences = 3, $variableNbSentences = true)
 * @method static string|array paragraphs($nb = 3, $asText = false)
 * @method static string text($maxNbChars = 200)
 * @method static string realText($maxNbChars = 200, $indexSize = 2)
 * @method static string password($minLength = 6, $maxLength = 20)
 * @method static string slug($nbWords = 6, $variableNbWords = true)
 * @method static string amPm($max = 'now')
 * @method static string date($format = 'Y-m-d', $max = 'now')
 * @method static string dayOfMonth($max = 'now')
 * @method static string dayOfWeek($max = 'now')
 * @method static string iso8601($max = 'now')
 * @method static string month($max = 'now')
 * @method static string monthName($max = 'now')
 * @method static string time($format = 'H:i:s', $max = 'now')
 * @method static int unixTime($max = 'now')
 * @method static string year($max = 'now')
 * @method static \DateTime dateTime($max = 'now', $timezone = null)
 * @method static \DateTime dateTimeAd($max = 'now', $timezone = null)
 * @method static \DateTime dateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null)
 * @method static \DateTime dateTimeInInterval($date = '-30 years', $interval = '+5 days', $timezone = null)
 * @method static \DateTime dateTimeThisCentury($max = 'now', $timezone = null)
 * @method static \DateTime dateTimeThisDecade($max = 'now', $timezone = null)
 * @method static \DateTime dateTimeThisYear($max = 'now', $timezone = null)
 * @method static \DateTime dateTimeThisMonth($max = 'now', $timezone = null)
 * @method static boolean boolean($chanceOfGettingTrue = 50)
 * @method static int randomNumber($nbDigits = null, $strict = false)
 * @method static int|string|null randomKey(array $array = [])
 * @method static int numberBetween($min = 0, $max = 2147483647)
 * @method static float randomFloat($nbMaxDecimals = null, $min = 0, $max = null)
 * @method static mixed randomElement(array $array = ['a', 'b', 'c'])
 * @method static array randomElements(array $array = ['a', 'b', 'c'], $count = 1, $allowDuplicates = false)
 * @method static array|string shuffle($arg = '')
 * @method static array shuffleArray(array $array = [])
 * @method static string shuffleString($string = '', $encoding = 'UTF-8')
 * @method static string numerify($string = '###')
 * @method static string lexify($string = '????')
 * @method static string bothify($string = '## ??')
 * @method static string asciify($string = '****')
 * @method static string regexify($regex = '')
 * @method static string toLower($string = '')
 * @method static string toUpper($string = '')
 * @method static FakerGenerator optional($weight = 0.5, $default = null)
 * @method static FakerGenerator unique($reset = false, $maxRetries = 10000)
 * @method static FakerGenerator valid($validator = null, $maxRetries = 10000)
 * @method static mixed passthrough($passthrough)
 * @method static integer biasedNumberBetween($min = 0, $max = 100, $function = 'sqrt')
 * @method static string file($sourceDirectory = '/tmp', $targetDirectory = '/tmp', $fullPath = true)
 * @method static string imageUrl($width = 640, $height = 480, $category = null, $randomize = true, $word = null, $gray = false)
 * @method static string image($dir = null, $width = 640, $height = 480, $category = null, $fullPath = true, $randomize = true, $word = null)
 * @method static string randomHtml($maxDepth = 4, $maxWidth = 4)
 */
class Faker extends Facade {

	/**
	 * @var FakerGenerator
	 */
	protected static $instance;

	/**
	 * 创建Facade实例
	 *
	 * @static
	 * @access protected
	 * @param string $class 类名或标识
	 * @param array  $args 变量
	 * @param bool   $newInstance 是否每次创建新的实例
	 * @return object
	 */
	protected static function createFacade(string $class = '', array $args = [], bool $newInstance = false) {
		if (self::$instance === null) {
			self::$instance = FakerFactory::create(
				Config::get('app.faker_locale')
			);
		}

		return self::$instance;
	}

	/**
	 * 带参数实例化当前Facade类
	 *
	 * @access public
	 * @param array $args
	 * @return FakerGenerator
	 */
	public static function instance(...$args) {
		return static::createFacade('', $args);
	}

}
