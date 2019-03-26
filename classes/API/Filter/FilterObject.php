<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API\Filter;

/**
 * Class FilterObject
 * @package QU\LERQ\API\Filter
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class FilterObject
{
	// <=
	const TIME_BEFORE = 0;
	// >=
	const TIME_AFTER = 1;

	/** @var string */
	private $course_start;
	/** @var int */
	private $course_start_direction;
	/** @var string */
	private $course_end;
	/** @var int */
	private $course_end_direction;
	/** @var string */
	private $progress;
	/** @var int */
	private $page_start;
	/** @var int */
	private $page_length;
	/** @var string */
	private $event_type;
	/** @var string */
	private $event_happened;
	/** @var int */
	private $event_happened_direction;
	/** @var string */
	private $assignment;
	/** @var string */
	private $event;

	/**
	 * @return int
	 */
	public function getCourseStartDirection(): int
	{
		return $this->course_start_direction;
	}

	/**
	 * @return string|bool
	 */
	public function getCourseStart()
	{
		return (isset($this->course_start) ? $this->course_start : false);
	}

	/**
	 * @param string $course_start UTC Timestamp
	 * @return FilterObject
	 */
	public function setCourseStart(string $course_start, int $before_after = self::TIME_AFTER): FilterObject
	{
		$this->course_start = $course_start;
		$this->course_start_direction = $before_after;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCourseEndDirection(): int
	{
		return $this->course_end_direction;
	}

	/**
	 * @return string|bool
	 */
	public function getCourseEnd()
	{
		return (isset($this->course_end) ? $this->course_end : false);
	}

	/**
	 * @param string $course_end UTC Timestamp
	 * @return FilterObject
	 */
	public function setCourseEnd(string $course_end, int $before_after = self::TIME_BEFORE): FilterObject
	{
		$this->course_end = $course_end;
		$this->course_end_direction = $before_after;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProgress(): string
	{
		return (isset($this->progress) ? $this->progress : '*');
	}

	/**
	 * @param string $process
	 * @return FilterObject
	 */
	public function setProgress(string $progress): FilterObject
	{
		$this->progress = $progress;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPageStart(): int
	{
		return (isset($this->page_start) ? $this->page_start : 0);
	}

	/**
	 * @param int $page_start
	 * @return FilterObject
	 */
	public function setPageStart(int $page_start): FilterObject
	{
		$this->page_start = $page_start;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPageLength(): int
	{
		return (isset($this->page_length) ? $this->page_length : 500);
	}

	/**
	 * @param int $page_length
	 * @return FilterObject
	 */
	public function setPageLength(int $page_length): FilterObject
	{
		$this->page_length = $page_length;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventType(): string
	{
		return (isset($this->event_type) ? $this->event_type : '*');
	}

	/**
	 * @param string $event_type
	 * @return FilterObject
	 */
	public function setEventType(string $event_type): FilterObject
	{
		$this->event_type = $event_type;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getEventHappenedDirection(): int
	{
		return $this->event_happened_direction;
	}

	/**
	 * @return string|bool
	 */
	public function getEventHappened()
	{
		return (isset($this->event_happened) ? $this->event_happened : false);
	}

	/**
	 * @param string $event_happened  UTC Timestamp
	 * @return FilterObject
	 */
	public function setEventHappened(string $event_happened, int $before_after = self::TIME_AFTER): FilterObject
	{
		$this->event_happened = $event_happened;
		$this->event_happened_direction = $before_after;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAssignment(): string
	{
		return (isset($this->assignment) ? $this->assignment : '*');
	}

	/**
	 * @param string $assignment
	 * @return FilterObject
	 */
	public function setAssignment(string $assignment): FilterObject
	{
		$this->assignment = $assignment;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEvent(): string
	{
		return (isset($this->event) ? $this->event : '*');
	}

	/**
	 * @param string $event
	 * @return FilterObject
	 */
	public function setEvent(string $event): FilterObject
	{
		$this->event = $event;
		return $this;
	}


}