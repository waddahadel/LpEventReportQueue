<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Model;

/**
 * Class MemberModel
 * @package QU\LERQ\Model
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class MemberModel
{
	/** @var string */
	private $member_role;
	/** @var string */
	private $course_title;
	/** @var int */
	private $course_id;
	/** @var int */
	private $course_ref_id;

	/**
	 * @return string
	 */
	public function getMemberRole(): string
	{
		return $this->member_role;
	}

	/**
	 * @param string $member_role
	 * @return MemberModel
	 */
	public function setMemberRole(string $member_role): MemberModel
	{
		$this->member_role = $member_role;
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
	 * @return MemberModel
	 */
	public function setCourseTitle(string $course_title): MemberModel
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
	 * @return MemberModel
	 */
	public function setCourseId(int $course_id): MemberModel
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
	 * @return MemberModel
	 */
	public function setCourseRefId(int $course_ref_id): MemberModel
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
			'role' => $this->getMemberRole(),
			'course_title' => $this->getCourseTitle(),
			'course_id' => $this->getCourseId(),
			'course_ref_id' => $this->getCourseRefId(),
		]);
	}
}