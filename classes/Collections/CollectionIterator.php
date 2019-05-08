<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Collections;

/**
 * Class CollectionIterator
 * @package QU\LERQ\Collections
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class CollectionIterator implements \Iterator
{
	/** @var array  */
	private $items;

	/**
	 * CollectionIterator constructor.
	 * @param array $items
	 */
	public function __construct(array $items)
	{
		$this->items = $items;
	}

	/**
	 * @return mixed
	 */
	public function current()
	{
		$item = current($this->items);
		return $item;
	}

	/**
	 * @return void
	 */
	public function next()
	{
		next($this->items);
	}

	/**
	 * @return int|mixed|string|null
	 */
	public function key()
	{
		$key = key($this->items);
		return $key;
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		$valid = $this->current() !== false;
		return $valid;
	}

	/**
	 * @return void
	 */
	public function rewind()
	{
		$item = reset($this->items);
	}

}