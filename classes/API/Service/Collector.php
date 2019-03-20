<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API\Service;

use QU\LERQ\Collections\QueueCollection;

/**
 * Class Collector
 * @package QU\LERQ\Queue
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class Collector
{
	/** @var \QU\LERQ\API\Filter\FilterObject  */
	private $filter;
	/** @var \ilDBInterface  */
	private $database;

	/**
	 * Collector constructor.
	 * @param \QU\LERQ\API\Filter\FilterObject $filter
	 */
	public function __construct(\QU\LERQ\API\Filter\FilterObject $filter)
	{
		global $DIC;

		$this->filter = $filter;
		$this->database = $DIC->database();
	}

	/**
	 * @return QueueCollection
	 */
	public function collect(): QueueCollection
	{
		$collection = new QueueCollection();
		$items = [];

		$query = $this->createSelect();
		$query .= $this->createWhereByFilter();

		// @Todo change array to object?
		// @Todo convert json fields to array/object?
		$this->queryQueue($query, $items);

		return $collection->create($items);
	}

	/**
	 * @return string
	 */
	private function createSelect()
	{
		$select = 'SELECT ';
		$select .= '`id`, `timestamp`, `event`, `event_type`, `progress`, `assignment`, ';
		$select .= '`course_start`, `course_end`, `user_data`, `obj_data`, `mem_data` ';
		$select .= 'FROM `lp_event_report_queue` ';

		return $select;
	}

	/**
	 * @return string
	 */
	private function createWhereByFilter(): string
	{
		$where = '';

		if ($this->filter->getCourseStart() !== false) {
//			$where .= '`course_start` >= ' . ; // @ToDo how do we save the timestamps?
			$where .= ' AND ';
		}
		if ($this->filter->getCourseEnd() !== false) {
//			$where .= '`course_end` >= ' . ; // @ToDo how do we save the timestamps?
			$where .= ' AND ';
		}
		if ($this->filter->getProgress() !== '*') {
			$where .= '`event_type` = "lp_event"';
			$where .= '`progress` = "' . $this->database->quote($this->filter->getProgress(), 'text') . '" ';
			$where .= ' AND ';
		}
		if ($this->filter->getPageStart() !== 0) {
			$where .= '`id` >= ' . $this->database->quote($this->filter->getPageStart(), 'integer') . ' ';
			$where .= ' AND ';
		}
		if ($this->filter->getEventType() !== '*' && $this->filter->getProgress() == '*') {
			$where .= '`event_type` = "' . $this->database->quote($this->filter->getEventType(), 'text') . '" ';
			$where .= ' AND ';
		}
		if ($this->filter->getPageLength() !== -1) {
			$where .= '';
			$where .= ' LIMIT ' . $this->database->quote($this->filter->getPageLength(), 'integer') . ' ';
		}

		if (strlen($where) > 0) {
			$where = ' WHERE ' . $where;
		}

		return $where;
	}

	/**
	 * @param string $query
	 * @param array $items
	 * @return void
	 */
	private function queryQueue(string $query, array &$items = [])
	{
		$res = $this->database->query($query);

		while ($row = $this->database->fetchAssoc($res)) {
			$items[] = $row;
		}
	}
}