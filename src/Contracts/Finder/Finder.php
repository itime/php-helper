<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Finder;

interface Finder
{

	/**
	 * Hint path delimiter value.
	 *
	 * @var string
	 */
	const HINT_PATH_DELIMITER = '@';

	/**
	 * Get the fully qualified location of the file.
	 *
	 * @param string $file
	 * @return string
	 */
	public function find($file);

	/**
	 * Add a location to the finder.
	 *
	 * @param string $location
	 * @return void
	 */
	public function addLocation($location);

	/**
	 * Add a namespace hint to the finder.
	 *
	 * @param string $namespace
	 * @param string|array $hints
	 * @return void
	 */
	public function addNamespace($namespace, $hints);

	/**
	 * Prepend a namespace hint to the finder.
	 *
	 * @param string $namespace
	 * @param string|array $hints
	 * @return void
	 */
	public function prependNamespace($namespace, $hints);

	/**
	 * Replace the namespace hints for the given namespace.
	 *
	 * @param string $namespace
	 * @param string|array $hints
	 * @return void
	 */
	public function replaceNamespace($namespace, $hints);

	/**
	 * Add a valid file extension to the finder.
	 *
	 * @param string $extension
	 * @return void
	 */
	public function addExtension($extension);

	/**
	 * Flush the cache of located files.
	 *
	 * @return void
	 */
	public function flush();

}
