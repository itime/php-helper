<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth\Access\Rule;

use Xin\Contracts\Auth\Access\AuthenticRule as AuthenticRuleContract;

class AuthenticRule implements AuthenticRuleContract
{

	/**
	 * 规则方案
	 *
	 * @var string
	 */
	private $scheme = "";

	/**
	 * 规则实体
	 *
	 * @var string
	 */
	private $rule = '';

	/**
	 * 规则参数
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * AuthenticRule constructor.
	 *
	 * @param string $scheme
	 * @param string $rule
	 * @param array $options
	 */
	public function __construct($scheme, $rule, array $options = [])
	{
		$this->scheme = $scheme;
		$this->rule = $rule;
		$this->options = $options;
	}

	/**
	 * 获取规则方案
	 *
	 * @return string
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * 获取规则实体
	 *
	 * @return string
	 */
	public function getEntity()
	{
		return $this->rule;
	}

	/**
	 * 获取扩展参数
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->scheme . ':' . $this->rule;
	}

	/**
	 * 解析字符串规则
	 *
	 * @param string $fullRule
	 * @return static
	 * @throws \Xin\Auth\Rule\AuthenticRuleException
	 */
	public static function parse($fullRule)
	{
		if (empty($fullRule)) {
			throw new AuthenticRuleException("authentic rule invalid.");
		}

		$result = parse_url($fullRule);

		if (!isset($result['scheme']) || empty($scheme = $result['scheme'])) {
			throw new AuthenticRuleException("authentic rule scheme invalid.");
		}

		$path = ($result['host'] ?: '') . ($result['path'] ?: '');
		if (empty($path)) {
			throw new AuthenticRuleException("authentic rule path invalid.");
		}

		$options = $result['query'] ?: [];
		if (is_string($options)) {
			parse_str($options, $options);
		}

		return new static($scheme, $path, $options);
	}

}
