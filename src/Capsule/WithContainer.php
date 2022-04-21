<?php

namespace Xin\Capsule;

use Psr\Container\ContainerInterface;

trait WithContainer
{

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * 生成类实例
	 * @param string $class
	 * @param array $args
	 * @return mixed
	 */
	protected function makeClassInstance($class, $args = [])
	{
		if (method_exists($this->container, 'make')) {
			return $this->container->make($class, $args);
		}

		if (method_exists($class, '__make')) {
			return call_user_func_array([$class, '__make'], $args);
		}

		return $this->container->get($class);
	}

}
