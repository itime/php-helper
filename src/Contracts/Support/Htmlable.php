<?php

namespace Xin\Contracts\Support;

interface Htmlable{
	
	/**
	 * Get content as a string of HTML.
	 *
	 * @return string
	 */
	public function toHtml();
}
