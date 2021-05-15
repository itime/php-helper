<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 晋<657306123@qq.com>
 */
namespace Xin\Support;

class Collection extends Fluent implements \Countable, \IteratorAggregate{

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator.
	 *
	 * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return \ArrayIterator An instance of an object implementing <b>Iterator</b> or
	 *                        <b>Traversable</b>
	 */
	public function getIterator(){
		return new \ArrayIterator($this->items);
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Count elements of an object.
	 *
	 * @see http://php.net/manual/en/countable.count.php
	 * @return int the custom count as an integer.
	 *             </p>
	 *             <p>
	 *             The return value is cast to an integer
	 */
	public function count(){
		return count($this->items);
	}

	/**
	 * var_export.
	 *
	 * @return array
	 */
	public function __set_state(){
		return $this->all();
	}
}
