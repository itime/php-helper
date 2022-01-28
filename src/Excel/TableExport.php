<?php

namespace Xin\Excel;

class TableExport implements Exportable
{

	/**
	 * @var Column[]
	 */
	protected $columns;

	/**
	 * @var \iterable
	 */
	protected $data;

	/**
	 * @var callable
	 */
	protected $dataResolver;

	/**
	 * @param Column[] $columns
	 * @param iterable $data
	 */
	public function __construct(array $columns, iterable $data = null)
	{
		$this->columns = $columns;
		$this->data = $data;
	}

	/**
	 * @inheritDoc
	 */
	public function columns()
	{
		return $this->columns;
	}

	/**
	 * @inheritDoc
	 */
	public function data($page = 1)
	{
		if ($this->dataResolver) {
			return call_user_func_array($this->dataResolver, [$page]);
		}

		return $this->data;
	}

	/**
	 * @inheritDoc
	 */
	public function chunkSize()
	{
		return 0;
	}

	/**
	 * @return callable
	 */
	public function getDataResolver()
	{
		return $this->dataResolver;
	}

	/**
	 * @param callable $dataResolver
	 */
	public function setDataResolver(callable $dataResolver)
	{
		$this->dataResolver = $dataResolver;
	}

}
