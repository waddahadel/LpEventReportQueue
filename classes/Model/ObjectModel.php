<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Model;

/**
 * Class ObjectModel
 * @package QU\LERQ\Model
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class ObjectModel
{
	/** @var string */
	private $title;
	/** @var int */
	private $id;
	/** @var int */
	private $ref_id;
	/** @var string */
	private $link;
	/** @var string */
	private $type;
	/** @var string */
	private $type_hr;
	/** @var string */
	private $course_title;
	/** @var int */
	private $course_id;
	/** @var int */
	private $course_ref_id;

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return ObjectModel
	 */
	public function setTitle(string $title): ObjectModel
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return ObjectModel
	 */
	public function setId(int $id): ObjectModel
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRefId(): int
	{
		return $this->ref_id;
	}

	/**
	 * @param int $ref_id
	 * @return ObjectModel
	 */
	public function setRefId(int $ref_id): ObjectModel
	{
		$this->ref_id = $ref_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLink(): string
	{
		return $this->link;
	}

	/**
	 * @param string $link
	 * @return ObjectModel
	 */
	public function setLink(string $link): ObjectModel
	{
		$this->link = $link;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return ObjectModel
	 */
	public function setType(string $type): ObjectModel
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTypeHr(): string
	{
		return $this->type_hr;
	}

	/**
	 * @param string $type_hr
	 * @return ObjectModel
	 */
	public function setTypeHr(string $type_hr): ObjectModel
	{
		$this->type_hr = $type_hr;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCourseTitle(): string
	{
		return $this->course_title;
	}

	/**
	 * @param string $course_title
	 * @return ObjectModel
	 */
	public function setCourseTitle(string $course_title): ObjectModel
	{
		$this->course_title = $course_title;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCourseId(): int
	{
		return $this->course_id;
	}

	/**
	 * @param int $course_id
	 * @return ObjectModel
	 */
	public function setCourseId(int $course_id): ObjectModel
	{
		$this->course_id = $course_id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCourseRefId(): int
	{
		return $this->course_ref_id;
	}

	/**
	 * @param int $course_ref_id
	 * @return ObjectModel
	 */
	public function setCourseRefId(int $course_ref_id): ObjectModel
	{
		$this->course_ref_id = $course_ref_id;
		return $this;
	}

	/**
	 * @return false|string
	 */
	public function __toString()
	{
		return json_encode([
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'ref_id' => $this->getRefId(),
			'link' => $this->getLink(),
			'type' => $this->getTypeHr(),
			'course_title' => $this->getCourseTitle(),
			'course_id' => $this->getCourseId(),
			'course_ref_id' => $this->getCourseRefId(),
		]);
	}
}