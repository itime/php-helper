<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\View\Concerns;

use Exception;

/**
 * Trait Parser
 *
 * @mixin \Xin\Thinkphp\View\Template
 */
trait TemplateParser
{

	/**
	 * 保留内容信息
	 *
	 * @var array
	 */
	protected $literal = [];

	/**
	 * 模板解析入口
	 * 支持普通标签和TagLib解析 支持自定义标签库
	 *
	 * @access public
	 * @param string $content 要解析的模板内容
	 * @return void
	 * @noinspection PhpDocMissingThrowsInspection
	 */
	public function parse(string &$content): void
	{
		// 内容为空不解析
		if (empty($content)) {
			return;
		}

		// 替换literal标签内容
		$this->parseLiteral($content);

		// 解析继承
		$this->parseExtend($content);

		// 解析布局
		$this->parseLayout($content);

		// 检查include语法
		$this->parseInclude($content);

		// 替换包含文件中literal标签内容
		$this->parseLiteral($content);

		// 检查PHP语法
		$this->parsePhp($content);

		// 获取需要引入的标签库列表
		// 标签库只需要定义一次，允许引入多个一次
		// 一般放在文件的最前面
		// 格式：<taglib name="html,mytag..." />
		// 当TAGLIB_LOAD配置为true时才会进行检测
		if ($this->config['taglib_load']) {
			$tagLibs = $this->getIncludeTagLib($content);

			if (!empty($tagLibs)) {
				// 对导入的TagLib进行解析
				foreach ($tagLibs as $tagLibName) {
					$this->parseTagLib($tagLibName, $content);
				}
			}
		}

		// 预先加载的标签库 无需在每个模板中使用taglib标签加载 但必须使用标签库XML前缀
		if ($this->config['taglib_pre_load']) {
			$tagLibs = explode(',', $this->config['taglib_pre_load']);

			foreach ($tagLibs as $tag) {
				$this->parseTagLib($tag, $content);
			}
		}

		// 内置标签库 无需使用taglib标签导入就可以使用 并且不需使用标签库XML前缀
		$tagLibs = explode(',', $this->config['taglib_build_in']);

		foreach ($tagLibs as $tag) {
			$this->parseTagLib($tag, $content, true);
		}

		// 解析普通模板标签 {$tagName}
		$this->parseTag($content);

		// 解析指令标签
		$this->parseDirective($content);

		// 还原被替换的Literal标签
		$this->parseLiteral($content, true);
	}

	/**
	 * 检查PHP语法
	 *
	 * @access private
	 * @param string $content 要解析的模板内容
	 * @return void
	 * @throws \Exception
	 */
	protected function parsePhp(string &$content): void
	{
		// 短标签的情况要将<?标签用echo方式输出 否则无法正常输出xml标识
		$content = preg_replace('/(<\?(?!php|=|$))/i', '<?php echo \'\\1\'; ?>' . "\n", $content);

		// PHP语法检查
		if ($this->config['tpl_deny_php'] && false !== strpos($content, '<?php')) {
			throw new Exception('not allow php tag');
		}
	}

	/**
	 * 解析模板中的布局标签
	 *
	 * @access private
	 * @param string $content 要解析的模板内容
	 * @return void
	 */
	protected function parseLayout(string &$content): void
	{
		// 读取模板中的布局标签
		if (preg_match($this->getRegex('layout'), $content, $matches)) {
			// 替换Layout标签
			$content = str_replace($matches[0], '', $content);
			// 解析Layout标签
			$array = $this->parseAttr($matches[0]);

			if (!$this->config['layout_on'] || $this->config['layout_name'] != $array['name']) {
				// 读取布局模板
				$layoutFile = $this->parseTemplateFile($array['name']);

				if ($layoutFile) {
					$replace = isset($array['replace']) ? $array['replace'] : $this->config['layout_item'];
					// 替换布局的主体内容
					$content = str_replace($replace, $content, file_get_contents($layoutFile));
				}
			}
		} else {
			$content = str_replace('{__NOLAYOUT__}', '', $content);
		}
	}

	/**
	 * 解析模板中的include标签
	 *
	 * @access private
	 * @param string $content 要解析的模板内容
	 * @return void
	 */
	protected function parseInclude(string &$content): void
	{
		$regex = $this->getRegex('include');
		$func = function ($template) use (&$func, &$regex, &$content) {
			if (preg_match_all($regex, $template, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					$array = $this->parseAttr($match[0]);
					$file = $array['file'];
					unset($array['file']);

					// 分析模板文件名并读取内容
					$parseStr = $this->parseTemplateName($file);

					foreach ($array as $k => $v) {
						// 以$开头字符串转换成模板变量
						if (0 === strpos($v, '$')) {
							$v = $this->get(substr($v, 1));
						}

						$parseStr = str_replace('[' . $k . ']', $v, $parseStr);
					}

					$content = str_replace($match[0], $parseStr, $content);
					// 再次对包含文件进行模板分析
					$func($parseStr);
				}
				unset($matches);
			}
		};

		// 替换模板中的include标签
		$func($content);
	}

	/**
	 * 解析模板中的extend标签
	 *
	 * @access private
	 * @param string $content 要解析的模板内容
	 * @throws \Exception
	 */
	protected function parseExtend(string &$content): void
	{
		$regex = $this->getRegex('extend');
		$array = $blocks = $baseBlocks = [];
		$extend = '';

		$func = function ($template) use (&$func, &$regex, &$array, &$extend, &$blocks, &$baseBlocks) {
			if (preg_match($regex, $template, $matches)) {
				if (!isset($array[$matches['name']])) {
					$array[$matches['name']] = 1;
					// 读取继承模板
					$extend = $this->parseTemplateName($matches['name']);

					// 递归检查继承
					$func($extend);

					// 取得block标签内容
					$blocks = array_merge($blocks, $this->parseBlock($template));

					return;
				}
			} else {
				// 取得顶层模板block标签内容
				$baseBlocks = $this->parseBlock($template, true);

				if (empty($extend)) {
					// 无extend标签但有block标签的情况
					$extend = $template;
				}
			}
		};

		$func($content);

		if (!empty($extend)) {
			if ($baseBlocks) {
				$children = [];
				foreach ($baseBlocks as $name => $val) {
					$replace = $val['content'];

					if (!empty($children[$name])) {
						// 如果包含有子block标签
						foreach ($children[$name] as $key) {
							$replace = str_replace($baseBlocks[$key]['begin'] . $baseBlocks[$key]['content'] . $baseBlocks[$key]['end'], $blocks[$key]['content'], $replace);
						}
					}

					if (isset($blocks[$name])) {
						// 带有{__block__}表示与所继承模板的相应标签合并，而不是覆盖
						$replace = str_replace(['{__BLOCK__}', '{__block__}'], $replace, $blocks[$name]['content']);

						if (!empty($val['parent'])) {
							// 如果不是最顶层的block标签
							$parent = $val['parent'];

							if (isset($blocks[$parent])) {
								$blocks[$parent]['content'] = str_replace($blocks[$name]['begin'] . $blocks[$name]['content'] . $blocks[$name]['end'], $replace, $blocks[$parent]['content']);
							}

							$blocks[$name]['content'] = $replace;
							$children[$parent][] = $name;

							continue;
						}
					} elseif (!empty($val['parent'])) {
						// 如果子标签没有被继承则用原值
						$children[$val['parent']][] = $name;
						$blocks[$name] = $val;
					}

					if (!$val['parent']) {
						// 替换模板中的顶级block标签
						$extend = str_replace($val['begin'] . $val['content'] . $val['end'], $replace, $extend);
					}
				}
			}

			$content = $extend;
			unset($blocks, $baseBlocks);
		}
	}

	/**
	 * 替换页面中的literal标签
	 *
	 * @access private
	 * @param string $content 模板内容
	 * @param boolean $restore 是否为还原
	 * @return void
	 */
	protected function parseLiteral(string &$content, bool $restore = false): void
	{
		$regex = $this->getRegex($restore ? 'restoreliteral' : 'literal');

		if (preg_match_all($regex, $content, $matches, PREG_SET_ORDER)) {
			if (!$restore) {
				$count = count($this->literal);

				// 替换literal标签
				foreach ($matches as $match) {
					$this->literal[] = substr($match[0], strlen($match[1]), -strlen($match[2]));
					$content = str_replace($match[0], "<!--###literal{$count}###-->", $content);
					$count++;
				}
			} else {
				// 还原literal标签
				foreach ($matches as $match) {
					$content = str_replace($match[0], $this->literal[$match[1]], $content);
				}

				// 清空literal记录
				$this->literal = [];
			}

			unset($matches);
		}
	}

	/**
	 * 获取模板中的block标签
	 *
	 * @access private
	 * @param string $content 模板内容
	 * @param boolean $sort 是否排序
	 * @return array
	 */
	protected function parseBlock(string &$content, bool $sort = false): array
	{
		$regex = $this->getRegex('block');
		$result = [];

		if (preg_match_all($regex, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
			$right = $keys = [];

			foreach ($matches as $match) {
				if (empty($match['name'][0])) {
					if (count($right) > 0) {
						$tag = array_pop($right);
						$start = $tag['offset'] + strlen($tag['tag']);
						$length = $match[0][1] - $start;

						$result[$tag['name']] = [
							'begin' => $tag['tag'],
							'content' => substr($content, $start, $length),
							'end' => $match[0][0],
							'parent' => count($right) ? end($right)['name'] : '',
						];

						$keys[$tag['name']] = $match[0][1];
					}
				} else {
					// 标签头压入栈
					$right[] = [
						'name' => $match[2][0],
						'offset' => $match[0][1],
						'tag' => $match[0][0],
					];
				}
			}

			unset($right, $matches);

			if ($sort) {
				// 按block标签结束符在模板中的位置排序
				array_multisort($keys, $result);
			}
		}

		return $result;
	}

	/**
	 * 搜索模板页面中包含的TagLib库
	 * 并返回列表
	 *
	 * @access private
	 * @param string $content 模板内容
	 * @return array|void
	 */
	protected function getIncludeTagLib(string &$content)
	{
		// 搜索是否有TagLib标签
		if (preg_match($this->getRegex('taglib'), $content, $matches)) {
			// 替换TagLib标签
			$content = str_replace($matches[0], '', $content);

			return explode(',', $matches['name']);
		}
	}

	/**
	 * TagLib库解析
	 *
	 * @access public
	 * @param string $tagLib 要解析的标签库
	 * @param string $content 要解析的模板内容
	 * @param boolean $hide 是否隐藏标签库前缀
	 * @return void
	 */
	public function parseTagLib(string $tagLib, string &$content, bool $hide = false): void
	{
		if (false !== strpos($tagLib, '\\')) {
			// 支持指定标签库的命名空间
			$className = $tagLib;
			$tagLib = substr($tagLib, strrpos($tagLib, '\\') + 1);
		} else {
			$className = '\\Xin\\Thinkphp\\View\\TagLib\\' . ucwords($tagLib);
		}

		$tLib = new $className($this);

		$tLib->parseTag($content, $hide ? '' : $tagLib);
	}

	/**
	 * 分析标签属性
	 *
	 * @access public
	 * @param string $str 属性字符串
	 * @param string|null $name 不为空时返回指定的属性名
	 * @return array
	 */
	public function parseAttr(string $str, string $name = null): array
	{
		$regex = '/\s+(?>(?P<name>[\w-]+)\s*)=(?>\s*)([\"\'])(?P<value>(?:(?!\\2).)*)\\2/is';
		$array = [];

		if (preg_match_all($regex, $str, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$array[$match['name']] = $match['value'];
			}
			unset($matches);
		}

		if (!empty($name) && isset($array[$name])) {
			return $array[$name];
		}

		return $array;
	}

	/**
	 * 模板标签解析
	 * 格式： {TagName:args [|content] }
	 *
	 * @access private
	 * @param string $content 要解析的模板内容
	 * @return void
	 */
	protected function parseTag(string &$content): void
	{
		$regex = $this->getRegex('tag');

		if (preg_match_all($regex, $content, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$str = stripslashes($match[1]);
				$flag = substr($str, 0, 1);

				switch ($flag) {
					case '$':
						// 解析模板变量 格式 {$varName}
						// 是否带有?号
						if (false !== $pos = strpos($str, '?')) {
							$array = preg_split('/([!=]={1,2}|(?<!-)[><]={0,1})/', substr($str, 0, $pos), 2, PREG_SPLIT_DELIM_CAPTURE);
							$name = $array[0];

							$this->parseVar($name);
							//$this->parseVarFunction($name);

							$str = trim(substr($str, $pos + 1));
							$this->parseVar($str);
							$first = substr($str, 0, 1);

							if (strpos($name, ')')) {
								// $name为对象或是自动识别，或者含有函数
								if (isset($array[1])) {
									$this->parseVar($array[2]);
									$name .= $array[1] . $array[2];
								}

								switch ($first) {
									case '?':
										$this->parseVarFunction($name);
										$str = '<?php echo (' . $name . ') ? ' . $name . ' : ' . substr($str, 1) . '; ?>';
										break;
									case '=':
										$str = '<?php if(' . $name . ') echo ' . substr($str, 1) . '; ?>';
										break;
									default:
										$str = '<?php echo ' . $name . '?' . $str . '; ?>';
								}
							} else {
								if (isset($array[1])) {
									$express = true;
									$this->parseVar($array[2]);
									$express = $name . $array[1] . $array[2];
								} else {
									$express = false;
								}

								if (in_array($first, ['?', '=', ':'])) {
									$str = trim(substr($str, 1));
									if ('$' == substr($str, 0, 1)) {
										$str = $this->parseVarFunction($str);
									}
								}

								// $name为数组
								switch ($first) {
									case '?':
										// {$varname??'xxx'} $varname有定义则输出$varname,否则输出xxx
										$str = '<?php echo ' . ($express ?: 'isset(' . $name . ')') . ' ? ' . $this->parseVarFunction($name) . ' : ' . $str . '; ?>';
										break;
									case '=':
										// {$varname?='xxx'} $varname为真时才输出xxx
										$str = '<?php if(' . ($express ?: '!empty(' . $name . ')') . ') echo ' . $str . '; ?>';
										break;
									case ':':
										// {$varname?:'xxx'} $varname为真时输出$varname,否则输出xxx
										$str = '<?php echo ' . ($express ?: '!empty(' . $name . ')') . ' ? ' . $this->parseVarFunction($name) . ' : ' . $str . '; ?>';
										break;
									default:
										if (strpos($str, ':')) {
											// {$varname ? 'a' : 'b'} $varname为真时输出a,否则输出b
											$array = explode(':', $str, 2);

											$array[0] = '$' == substr(trim($array[0]), 0, 1) ? $this->parseVarFunction($array[0]) : $array[0];
											$array[1] = '$' == substr(trim($array[1]), 0, 1) ? $this->parseVarFunction($array[1]) : $array[1];

											$str = implode(' : ', $array);
										}
										$str = '<?php echo ' . ($express ?: '!empty(' . $name . ')') . ' ? ' . $str . '; ?>';
								}
							}
						} else {
							$this->parseVar($str);
							$this->parseVarFunction($str);
							$str = '<?php echo ' . $str . '; ?>';
						}
						break;
					case ':':
						// 输出某个函数的结果
						$str = substr($str, 1);
						$this->parseVar($str);
						$str = '<?php echo ' . $str . '; ?>';
						break;
					case '~':
						// 执行某个函数
						$str = substr($str, 1);
						$this->parseVar($str);
						$str = '<?php ' . $str . '; ?>';
						break;
					case '-':
					case '+':
						// 输出计算
						$this->parseVar($str);
						$str = '<?php echo ' . $str . '; ?>';
						break;
					case '/':
						// 注释标签
						$flag2 = substr($str, 1, 1);
						if ('/' == $flag2 || ('*' == $flag2 && substr(rtrim($str), -2) == '*/')) {
							$str = '';
						}
						break;
					default:
						// 未识别的标签直接返回
						$str = $this->config['tpl_begin'] . $str . $this->config['tpl_end'];
						break;
				}

				$content = str_replace($match[0], $str, $content);
			}

			unset($matches);
		}
	}

	/**
	 * 解析指令
	 *
	 * @param string $content
	 */
	protected function parseDirective(string &$content): void
	{
		$regex = $this->getRegex('@');
		if (!preg_match_all($regex, $content, $matches, PREG_SET_ORDER)) {
			return;
		}

		foreach ($matches as $match) {
			$directive = stripslashes($match[1]);

			// 指令
			if (isset($this->directive[$directive])) {
				$callback = $this->directive[$directive];
				$parseStr = $callback($match[2]);

				$content = str_replace($match[0], $parseStr, $content);
			}
		}

		unset($matches);
	}

	/**
	 * 模板变量解析,支持使用函数
	 * 格式： {$varname|function1|function2=arg1,arg2}
	 *
	 * @access public
	 * @param string $varStr 变量数据
	 * @return void
	 */
	public function parseVar(string &$varStr): void
	{
		$varStr = trim($varStr);

		if (preg_match_all('/\$[a-zA-Z_](?>\w*)(?:[:\.][0-9a-zA-Z_](?>\w*))+/', $varStr, $matches, PREG_OFFSET_CAPTURE)) {
			static $_varParseList = [];

			while ($matches[0]) {
				$match = array_pop($matches[0]);

				//如果已经解析过该变量字串，则直接返回变量值
				if (isset($_varParseList[$match[0]])) {
					$parseStr = $_varParseList[$match[0]];
				} else {
					if (strpos($match[0], '.')) {
						$vars = explode('.', $match[0]);
						$first = array_shift($vars);

						if (isset($this->extend[$first])) {
							$callback = $this->extend[$first];
							$parseStr = $callback($vars);
						} elseif ('$Request' == $first) {
							// 输出请求变量
							$parseStr = $this->parseRequestVar($vars);
						} elseif ('$Think' == $first) {
							// 所有以Think.打头的以特殊变量对待 无需模板赋值就可以输出
							$parseStr = $this->parseThinkVar($vars);
						} else {
							switch ($this->config['tpl_var_identify']) {
								case 'array': // 识别为数组
									$parseStr = $first . '[\'' . implode('\'][\'', $vars) . '\']';
									break;
								case 'obj': // 识别为对象
									$parseStr = $first . '->' . implode('->', $vars);
									break;
								default: // 自动判断数组或对象
									$parseStr = '(is_array(' . $first . ')?' . $first . '[\'' . implode('\'][\'', $vars) . '\']:' . $first . '->' . implode('->', $vars) . ')';
							}
						}
					} else {
						$parseStr = str_replace(':', '->', $match[0]);
					}

					$_varParseList[$match[0]] = $parseStr;
				}

				$varStr = substr_replace($varStr, $parseStr, $match[1], strlen($match[0]));
			}
			unset($matches);
		}
	}

	/**
	 * 对模板中使用了函数的变量进行解析
	 * 格式 {$varname|function1|function2=arg1,arg2}
	 *
	 * @access public
	 * @param string $varStr 变量字符串
	 * @param bool $autoescape 自动转义
	 * @return string
	 */
	public function parseVarFunction(string &$varStr, bool $autoescape = true): string
	{
		if (!$autoescape && false === strpos($varStr, '|')) {
			return $varStr;
		} elseif ($autoescape && !preg_match('/\|(\s)?raw(\||\s)?/i', $varStr)) {
			$varStr .= '|' . $this->config['default_filter'];
		}

		static $_varFunctionList = [];

		$_key = md5($varStr);

		//如果已经解析过该变量字串，则直接返回变量值
		if (isset($_varFunctionList[$_key])) {
			$varStr = $_varFunctionList[$_key];
		} else {
			$varArray = explode('|', $varStr);

			// 取得变量名称
			$name = trim(array_shift($varArray));

			// 对变量使用函数
			$length = count($varArray);

			// 取得模板禁止使用函数列表
			$template_deny_funs = explode(',', $this->config['tpl_deny_func_list']);

			for ($i = 0; $i < $length; $i++) {
				$args = explode('=', $varArray[$i], 2);

				// 模板函数过滤
				$fun = trim($args[0]);
				if (in_array($fun, $template_deny_funs)) {
					continue;
				}

				switch (strtolower($fun)) {
					case 'raw':
						break;
					case 'date':
						$name = 'date(' . $args[1] . ',!is_numeric(' . $name . ')? strtotime(' . $name . ') : ' . $name . ')';
						break;
					case 'first':
						$name = 'current(' . $name . ')';
						break;
					case 'last':
						$name = 'end(' . $name . ')';
						break;
					case 'upper':
						$name = 'strtoupper(' . $name . ')';
						break;
					case 'lower':
						$name = 'strtolower(' . $name . ')';
						break;
					case 'format':
						$name = 'sprintf(' . $args[1] . ',' . $name . ')';
						break;
					case 'default': // 特殊模板函数
						if (false === strpos($name, '(')) {
							$name = '(isset(' . $name . ') && (' . $name . ' !== \'\')?' . $name . ':' . $args[1] . ')';
						} else {
							$name = '(' . $name . ' ?: ' . $args[1] . ')';
						}
						break;
					default: // 通用模板函数
						if (isset($args[1])) {
							if (strstr($args[1], '###')) {
								$args[1] = str_replace('###', $name, $args[1]);
								$name = "$fun($args[1])";
							} else {
								$name = "$fun($name,$args[1])";
							}
						} else {
							if (!empty($args[0])) {
								$name = "$fun($name)";
							}
						}
				}
			}

			$_varFunctionList[$_key] = $name;
			$varStr = $name;
		}

		return $varStr;
	}

	/**
	 * 请求变量解析
	 * 格式 以 $Request. 打头的变量属于请求变量
	 *
	 * @access public
	 * @param array $vars 变量数组
	 * @return string
	 */
	public function parseRequestVar(array $vars): string
	{
		$type = strtoupper(trim(array_shift($vars)));
		$param = implode('.', $vars);

		switch ($type) {
			case 'SERVER':
				$parseStr = '$_SERVER[\'' . $param . '\']';
				break;
			case 'GET':
				$parseStr = '$_GET[\'' . $param . '\']';
				break;
			case 'POST':
				$parseStr = '$_POST[\'' . $param . '\']';
				break;
			case 'COOKIE':
				$parseStr = '$_COOKIE[\'' . $param . '\']';
				break;
			case 'SESSION':
				$parseStr = '$_SESSION[\'' . $param . '\']';
				break;
			case 'ENV':
				$parseStr = '$_ENV[\'' . $param . '\']';
				break;
			case 'REQUEST':
				$parseStr = '$_REQUEST[\'' . $param . '\']';
				break;
			default:
				$parseStr = '\'\'';
		}

		return $parseStr;
	}

	/**
	 * 特殊模板变量解析
	 * 格式 以 $Think. 打头的变量属于特殊模板变量
	 *
	 * @access public
	 * @param array $vars 变量数组
	 * @return string
	 */
	public function parseThinkVar(array $vars): string
	{
		$type = strtoupper(trim(array_shift($vars)));
		$param = implode('.', $vars);

		switch ($type) {
			case 'CONST':
				$parseStr = strtoupper($param);
				break;
			case 'NOW':
				$parseStr = "date('Y-m-d g:i a',time())";
				break;
			case 'LDELIM':
				$parseStr = '\'' . ltrim($this->config['tpl_begin'], '\\') . '\'';
				break;
			case 'RDELIM':
				$parseStr = '\'' . ltrim($this->config['tpl_end'], '\\') . '\'';
				break;
			default:
				$parseStr = defined($type) ? $type : '\'\'';
		}

		return $parseStr;
	}

	/**
	 * 按标签生成正则
	 *
	 * @access private
	 * @param string $tagName 标签名
	 * @return string
	 */
	protected function getRegex(string $tagName): string
	{
		$regex = '';

		if ('tag' == $tagName) {
			$begin = $this->config['tpl_begin'];
			$end = $this->config['tpl_end'];

			if (strlen(ltrim($begin, '\\')) == 1 && strlen(ltrim($end, '\\')) == 1) {
				$regex = $begin . '((?:[\$]{1,2}[a-wA-w_]|[\:\~][\$a-wA-w_]|[+]{2}[\$][a-wA-w_]|[-]{2}[\$][a-wA-w_]|\/[\*\/])(?>[^' . $end . ']*))' . $end;
			} else {
				$regex = $begin . '((?:[\$]{1,2}[a-wA-w_]|[\:\~][\$a-wA-w_]|[+]{2}[\$][a-wA-w_]|[-]{2}[\$][a-wA-w_]|\/[\*\/])(?>(?:(?!' . $end . ').)*))' . $end;
			}
		} elseif ('@' == $tagName) {
			$regex = '\@([a-wA-w_]*)\(([\$a-wA-w_]*)\)';
			$regex = '\@(\w+(?:::\w+)?)\`([\w\W]+?)\`';
		} else {
			$begin = $this->config['taglib_begin'];
			$end = $this->config['taglib_end'];
			$single = strlen(ltrim($begin, '\\')) == 1 && strlen(ltrim($end, '\\')) == 1 ? true : false;

			switch ($tagName) {
				case 'block':
					if ($single) {
						$regex = $begin . '(?:' . $tagName . '\b\s+(?>(?:(?!name=).)*)\bname=([\'\"])(?P<name>[\$\w\-\/\.]+)\\1(?>[^' . $end . ']*)|\/' . $tagName . ')' . $end;
					} else {
						$regex = $begin . '(?:' . $tagName . '\b\s+(?>(?:(?!name=).)*)\bname=([\'\"])(?P<name>[\$\w\-\/\.]+)\\1(?>(?:(?!' . $end . ').)*)|\/' . $tagName . ')' . $end;
					}
					break;
				case 'literal':
					if ($single) {
						$regex = '(' . $begin . $tagName . '\b(?>[^' . $end . ']*)' . $end . ')';
						$regex .= '(?:(?>[^' . $begin . ']*)(?>(?!' . $begin . '(?>' . $tagName . '\b[^' . $end . ']*|\/' . $tagName . ')' . $end . ')' . $begin . '[^' . $begin . ']*)*)';
						$regex .= '(' . $begin . '\/' . $tagName . $end . ')';
					} else {
						$regex = '(' . $begin . $tagName . '\b(?>(?:(?!' . $end . ').)*)' . $end . ')';
						$regex .= '(?:(?>(?:(?!' . $begin . ').)*)(?>(?!' . $begin . '(?>' . $tagName . '\b(?>(?:(?!' . $end . ').)*)|\/' . $tagName . ')' . $end . ')' . $begin . '(?>(?:(?!' . $begin . ').)*))*)';
						$regex .= '(' . $begin . '\/' . $tagName . $end . ')';
					}
					break;
				case 'restoreliteral':
					$regex = '<!--###literal(\d+)###-->';
					break;
				case 'include':
					$name = 'file';
				case 'taglib':
				case 'layout':
				case 'extend':
					if (empty($name)) {
						$name = 'name';
					}
					if ($single) {
						$regex = $begin . $tagName . '\b\s+(?>(?:(?!' . $name . '=).)*)\b' . $name . '=([\'\"])(?P<name>[\$\w\-\/\.\:@,\\\\]+)\\1(?>[^' . $end . ']*)' . $end;
					} else {
						$regex = $begin . $tagName . '\b\s+(?>(?:(?!' . $name . '=).)*)\b' . $name . '=([\'\"])(?P<name>[\$\w\-\/\.\:@,\\\\]+)\\1(?>(?:(?!' . $end . ').)*)' . $end;
					}
					break;
			}
		}

		return '/' . $regex . '/is';
	}

}
