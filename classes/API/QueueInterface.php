<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API;

/**
 * Interface QueueInterface
 * @package QU\LERQ\API
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
interface QueueInterface
{
	/**
	 * Register a provider plugin
	 *
	 * @param string $name			Plugin Name
	 * @param string $namespace		Plugin Namespace
	 * @param string $path			Plugin root path (realpath)
	 * @param bool $hasOverrides	Does the plugin has overrides? (default: false)
	 * @return bool
	 */
	public function registerProvider(string $name, string $namespace, string $path, bool $hasOverrides = false): bool;

	/**
	 * Create a new Filter object
	 *
	 * All setter methods of the filter object are chainable.
	 * An filter object without defined filters, let the collection
	 * return everything, but with a limit of 500. To deactivate
	 * the page limit, use "->setPageLimit(-1)".
	 *
	 * Available methods:
	 * ->setCourseStart(string $course_start)
	 *   Filter for course | session start time
	 * ->setCourseEnd(string $course_end)
	 *   Filter for course | session end time
	 * ->setProgress(string $progress)
	 *   Filter for learning progress type (this locks eventType filter to 'lp_event')
	 * ->setPageStart(int $page_start)
	 *   Set id to start with
	 * ->setPageLength(int $page_length)
	 *   Set number of maximal entries | Default: 500
	 * ->setEventType(string $event_type)
	 *   Filter for specific event type
	 *
	 * @return Filter\FilterObject
	 */
	public function createFilterObject(): \QU\LERQ\API\Filter\FilterObject;

	/**
	 * Get a filtered Collection
	 *
	 * The QueueCollection object is iterable. It can be
	 * used in foreach loops like an normal array.
	 *
	 * Available methods:
	 * ->getAllItems()
	 *   Get all items from collection as array
	 * ->getItemKeys()
	 *   Get array keys for the current item (also works inside foreach)
	 * ->getIterator(bool $getnew)
	 *   Get the CollectionIterator object (a new instance if $getnew is true) | Default: false
	 *
	 * @param Filter\FilterObject $filter
	 * @return \QU\LERQ\Collections\QueueCollection
	 */
	public function getCollection(\QU\LERQ\API\Filter\FilterObject $filter): \QU\LERQ\Collections\QueueCollection;
}