<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Model;

class QueueModel
{
	/** @var int */
	private $id;
	/** @var string */
	private $timestamp;
	/** @var string („progress_changed“, „progress_reset“, etc.) */
	private $event;
	/** @var string ("lp_event", "member_event", "object_event") */
	private $event_type;
	/** @var string */
	private $progress;
	/** @var string */
	private $assignment;
	/** @var string */
	private $course_start;
	/** @var string */
	private $course_end;
	/** @var UserModel */
	private $user_data;
	/** @var ObjectModel */
	private $obj_data;
	/** @var MemberModel */
	private $mem_data;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return QueueModel
	 */
	public function setId(int $id): QueueModel
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTimestamp(): string
	{
		return $this->timestamp;
	}

	/**
	 * @param string $timestamp
	 * @return QueueModel
	 */
	public function setTimestamp(string $timestamp): QueueModel
	{
		$this->timestamp = $timestamp;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEvent(): string
	{
		return $this->event;
	}

	/**
	 * @param string $event
	 * @return QueueModel
	 */
	public function setEvent(string $event): QueueModel
	{
		$this->event = $event;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventType(): string
	{
		return $this->event_type;
	}

	/**
	 * @param string $event_type
	 * @return QueueModel
	 */
	public function setEventType(string $event_type): QueueModel
	{
		$this->event_type = $event_type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProgress(): string
	{
		return $this->progress;
	}

	/**
	 * @param string $progress
	 * @return QueueModel
	 */
	public function setProgress(string $progress): QueueModel
	{
		$this->progress = $progress;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAssignment(): string
	{
		return $this->assignment;
	}

	/**
	 * @param string $assignment
	 * @return QueueModel
	 */
	public function setAssignment(string $assignment): QueueModel
	{
		$this->assignment = $assignment;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCourseStart(): string
	{
		return $this->course_start;
	}

	/**
	 * @param string $course_start
	 * @return QueueModel
	 */
	public function setCourseStart(string $course_start): QueueModel
	{
		$this->course_start = $course_start;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCourseEnd(): string
	{
		return $this->course_end;
	}

	/**
	 * @param string $course_end
	 * @return QueueModel
	 */
	public function setCourseEnd(string $course_end): QueueModel
	{
		$this->course_end = $course_end;
		return $this;
	}

	/**
	 * @return UserModel
	 */
	public function getUserData(): UserModel
	{
		return $this->user_data;
	}

	/**
	 * @param UserModel $user_data
	 * @return QueueModel
	 */
	public function setUserData(UserModel $user_data): QueueModel
	{
		$this->user_data = $user_data;
		return $this;
	}

	/**
	 * @return ObjectModel
	 */
	public function getObjData(): ObjectModel
	{
		return $this->obj_data;
	}

	/**
	 * @param ObjectModel $obj_data
	 * @return QueueModel
	 */
	public function setObjData(ObjectModel $obj_data): QueueModel
	{
		$this->obj_data = $obj_data;
		return $this;
	}

	/**
	 * @return MemberModel
	 */
	public function getMemData(): MemberModel
	{
		return $this->mem_data;
	}

	/**
	 * @param MemberModel $mem_data
	 * @return QueueModel
	 */
	public function setMemData(MemberModel $mem_data): QueueModel
	{
		$this->mem_data = $mem_data;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return json_encode([
			'id' => $this->getId(),
			'timestamp' => $this->getTimestamp(),
			'event' => $this->getEvent(),
			'event_type' => $this->getEventType(),
			'progress' => $this->getProgress(),
			'assignment' => $this->getAssignment(),
			'course_start' => $this->getCourseStart(),
			'course_end' => $this->getCourseEnd(),
			'user_data' => $this->getUserData(),
			'obj_data' => $this->getObjData(),
			'mem_data' => $this->getMemData(),
		]);
	}


}