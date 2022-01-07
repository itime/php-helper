<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Finder;

use Xin\Contracts\Finder\Finder as FinderContract;

class FileFinder implements FinderContract {

	/**
	 * The array of active file paths.
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * The array of files that have been located.
	 *
	 * @var array
	 */
	protected $files = [];

	/**
	 * The namespace to file path hints.
	 *
	 * @var array
	 */
	protected $hints = [];

	/**
	 * Register a file extension with the finder.
	 *
	 * @var array
	 */
	protected $extensions = ['php', 'css', 'js', 'html'];

	/**
	 * Create a new file loader instance.
	 *
	 * @param array      $paths
	 * @param array|null $extensions
	 */
	public function __construct(array $paths, array $extensions = null) {
		$this->paths = array_map([$this, 'resolvePath'], $paths);

		if (isset($extensions)) {
			$this->extensions = $extensions;
		}
	}

	/**
	 * Get the fully qualified location of the file.
	 *
	 * @param string $name
	 * @return string
	 */
	public function find($name) {
		if (isset($this->files[$name])) {
			return $this->files[$name];
		}

		if ($this->hasHintInformation($name = trim($name))) {
			return $this->files[$name] = $this->findNamespacedFile($name);
		}

		return $this->files[$name] = $this->findInPaths($name, $this->paths);
	}

	/**
	 * Get the path to a template with a named path.
	 *
	 * @param string $name
	 * @return string
	 */
	protected function findNamespacedFile($name) {
		[$namespace, $file] = $this->parseNamespaceSegments($name);

		return $this->findInPaths($file, $this->hints[$namespace]);
	}

	/**
	 * Get the segments of a template with a named path.
	 *
	 * @param string $name
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	protected function parseNamespaceSegments($name) {
		$segments = explode(static::HINT_PATH_DELIMITER, $name);

		if (count($segments) !== 2) {
			throw new \InvalidArgumentException("File [{$name}] has an invalid name.");
		}

		if (!isset($this->hints[$segments[0]])) {
			throw new \InvalidArgumentException("No hint path defined for [{$segments[0]}].");
		}

		return $segments;
	}

	/**
	 * Find the given file in the list of paths.
	 *
	 * @param string $name
	 * @param array  $paths
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	protected function findInPaths($name, $paths) {
		foreach ((array)$paths as $path) {
			foreach ($this->getPossibleFiles($name) as $file) {
				if (file_exists($filePath = $path . '/' . $file)) {
					return $filePath;
				}
			}
		}

		throw new \InvalidArgumentException("File [{$name}] not found.");
	}

	/**
	 * Get an array of possible file files.
	 *
	 * @param string $name
	 * @return array
	 */
	protected function getPossibleFiles($name) {
		return array_map(function ($extension) use ($name) {
			return $name . '.' . $extension;
			// return str_replace('.', '/', $name).'.'.$extension;
		}, $this->extensions);
	}

	/**
	 * Add a location to the finder.
	 *
	 * @param string $location
	 * @return void
	 */
	public function addLocation($location) {
		$this->paths[] = $this->resolvePath($location);
	}

	/**
	 * Prepend a location to the finder.
	 *
	 * @param string $location
	 * @return void
	 */
	public function prependLocation($location) {
		array_unshift($this->paths, $this->resolvePath($location));
	}

	/**
	 * Resolve the path.
	 *
	 * @param string $path
	 * @return string
	 */
	protected function resolvePath($path) {
		return realpath($path) ?: $path;
	}

	/**
	 * Add a namespace hint to the finder.
	 *
	 * @param string       $namespace
	 * @param string|array $hints
	 * @return void
	 */
	public function addNamespace($namespace, $hints) {
		$hints = (array)$hints;
		$hints = array_map([$this, 'resolvePath'], $hints);

		if (isset($this->hints[$namespace])) {
			$hints = array_merge($this->hints[$namespace], $hints);
		}

		$this->hints[$namespace] = $hints;
	}

	/**
	 * Prepend a namespace hint to the finder.
	 *
	 * @param string       $namespace
	 * @param string|array $hints
	 * @return void
	 */
	public function prependNamespace($namespace, $hints) {
		$hints = (array)$hints;

		if (isset($this->hints[$namespace])) {
			$hints = array_merge($hints, $this->hints[$namespace]);
		}

		$this->hints[$namespace] = $hints;
	}

	/**
	 * Replace the namespace hints for the given namespace.
	 *
	 * @param string       $namespace
	 * @param string|array $hints
	 * @return void
	 */
	public function replaceNamespace($namespace, $hints) {
		$this->hints[$namespace] = (array)$hints;
	}

	/**
	 * Register an extension with the file finder.
	 *
	 * @param string $extension
	 * @return void
	 */
	public function addExtension($extension) {
		if (($index = array_search($extension, $this->extensions)) !== false) {
			unset($this->extensions[$index]);
		}

		array_unshift($this->extensions, $extension);
	}

	/**
	 * Returns whether or not the file name has any hint information.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasHintInformation($name) {
		return strpos($name, static::HINT_PATH_DELIMITER) > 0;
	}

	/**
	 * Flush the cache of located files.
	 *
	 * @return void
	 */
	public function flush() {
		$this->files = [];
	}

	/**
	 * Set the active file paths.
	 *
	 * @param array $paths
	 * @return $this
	 */
	public function setPaths($paths) {
		$this->paths = $paths;

		return $this;
	}

	/**
	 * Get the active file paths.
	 *
	 * @return array
	 */
	public function getPaths() {
		return $this->paths;
	}

	/**
	 * Get the files that have been located.
	 *
	 * @return array
	 */
	public function getFiles() {
		return $this->files;
	}

	/**
	 * Get the namespace to file path hints.
	 *
	 * @return array
	 */
	public function getHints() {
		return $this->hints;
	}

	/**
	 * Get registered extensions.
	 *
	 * @return array
	 */
	public function getExtensions() {
		return $this->extensions;
	}

}
