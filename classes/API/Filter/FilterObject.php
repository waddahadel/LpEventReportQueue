<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API\Filter;

class FilterObject
{
	/** @var string */
	private $course_start;
	/** @var string */
	private $course_end;
	/** @var string */
	private $progress;
	/** @var int */
	private $page_start;
	/** @var int */
	private $page_length;
	/** @var string */
	private $event_type;

	/**
	 * @return string|bool
	 */
	public function getCourseStart()
	{
		return (isset($this->course_start) ? $this->course_start : false);
	}

	/**
	 * @param string $course_start
	 * @return FilterObject
	 */
	public function setCourseStart(string $course_start): FilterObject
	{
		$this->course_start = $course_start;
		return $this;
	}

	/**
	 * @return string|bool
	 */
	public function getCourseEnd()
	{
		return (isset($this->course_end) ? $this->course_end : false);
	}

	/**
	 * @param string $course_end
	 * @return FilterObject
	 */
	public function setCourseEnd(string $course_end): FilterObject
	{
		$this->course_end = $course_end;
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


}