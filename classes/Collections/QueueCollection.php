<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Collections;

/**
 * Class QueueCollection
 * @package QU\LERQ\Collections
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class QueueCollection implements \IteratorAggregate
{
	/** @var array */
	private $items;
	/** @var CollectionIterator */
	private $iterator;

	/**
	 * @param array $items
	 * @return $this|bool
	 */
	public function create(array $items)
	{
		if (!is_array($items)) {
			return false;
		}

		$this->items = $items;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAllItems(): array
	{
		return $this->items;
	}

	/**
	 * @return array
	 */
	public function getItemKeys(): array
	{
		$current = $this->getIterator()->current();
		if (!is_array($current)) {
			$current = [];
		}
		return array_keys($current);
	}

	/**
	 * @param bool $getnew
	 * @return CollectionIterator|\Traversable
	 */
	public function getIterator($getnew = false)
	{
		if (!isset($this->iterator) || $getnew === true) {
			$this->iterator = new CollectionIterator($this->items);
		}
		return $this->iterator;
	}
}