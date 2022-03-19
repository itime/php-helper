<?php

namespace Xin\Thinkphp\Http;


use Xin\Support\Str;

trait HasContentTypes
{

	/**
	 * Determine if the current request probably expects a JSON response.
	 *
	 * @return bool
	 */
	public function expectsJson()
	{
		return ($this->isAjax() && !$this->isPjax() && $this->acceptsAnyContentType()) || $this->wantsJson();
	}

	/**
	 * Determine if the current request is asking for JSON.
	 *
	 * @return bool
	 */
	public function wantsJson()
	{
		$acceptable = $this->getAcceptableContentTypes();

		return isset($acceptable[0]) && Str::contains($acceptable[0], ['/json', '+json']);
	}

	/**
	 * Determines whether the current requests accepts a given content type.
	 *
	 * @param string|array $contentTypes
	 * @return bool
	 */
	public function accepts($contentTypes)
	{
		$accepts = $this->getAcceptableContentTypes();

		if (count($accepts) === 0) {
			return true;
		}

		$types = (array)$contentTypes;

		foreach ($accepts as $accept) {
			if ($accept === '*/*' || $accept === '*') {
				return true;
			}

			foreach ($types as $type) {
				if (self::matchesType($accept, $type) || $accept === strtok($type, '/') . '/*') {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Determine if the current request accepts any content type.
	 *
	 * @return bool
	 */
	public function acceptsAnyContentType()
	{
		$acceptable = $this->getAcceptableContentTypes();

		return count($acceptable) === 0 || (
				isset($acceptable[0]) && ($acceptable[0] === '*/*' || $acceptable[0] === '*')
			);
	}

	/**
	 * Determines whether a request accepts JSON.
	 *
	 * @return bool
	 */
	public function acceptsJson()
	{
		return $this->accepts('application/json');
	}

	/**
	 * Determines whether a request accepts HTML.
	 *
	 * @return bool
	 */
	public function acceptsHtml()
	{
		return $this->accepts('text/html');
	}

	/**
	 * Determine if the given content types match.
	 *
	 * @param string $actual
	 * @param string $type
	 * @return bool
	 */
	public static function matchesType($actual, $type)
	{
		if ($actual === $type) {
			return true;
		}

		$split = explode('/', $actual);

		return isset($split[1]) && preg_match('#' . preg_quote($split[0], '#') . '/.+\+' . preg_quote($split[1], '#') . '#', $type);
	}
}
