<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Queue;

use QU\LERQ\API\Filter\FilterObject;
use QU\LERQ\API\QueueInterface;

class API implements QueueInterface
{
	public function registerProvider(string $name, string $namespace, string $path, bool $hasOverrides = false): bool
	{
		$registration = new Registration();
		return $registration->create($name, $namespace, $path, $hasOverrides);
	}

	/**
	 * @return FilterObject
	 */
	public function createFilterObject(): \QU\LERQ\API\Filter\FilterObject
	{
		return new FilterObject();
	}

	/**
	 * @param FilterObject $filter
	 * @return \QU\LERQ\Collections\QueueCollection
	 */
	public function getCollection(\QU\LERQ\API\Filter\FilterObject $filter): \QU\LERQ\Collections\QueueCollection
	{
		$collector = new Collector($filter);
		return $collector->collect();
	}
}