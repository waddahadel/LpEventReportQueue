<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Model;

/**
 * Class EventModel
 * @package QU\LERQ\Model
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class EventModel
{
	/** @var int */
	private $obj_id;
	/** @var int */
	private $ref_id;
	/** @var int */
	private $parent_ref_id;
	/** @var string */
	private $obj_type;
	/** @var int */
	private $usr_id;
	/** @var int */
	private $role_id;
	/** @var array */
	private $appointments;
	/** @var string */
	private $lp_status;
	/** @var int */
	private $lp_percentage;
	/** @var string */
	private $event_name;

	/**
	 * @return int
	 */
	public function getObjId(): int
	{
		return (isset($this->obj_id) ? $this->obj_id : -1);
	}

	/**
	 * @param int $obj_id
	 * @return EventModel
	 */
	public function setObjId(int $obj_id): EventModel
	{
		$this->obj_id = $obj_id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRefId(): int
	{
		return (isset($this->ref_id) ? $this->ref_id : -1);
	}

	/**
	 * @param int $ref_id
	 * @return EventModel
	 */
	public function setRefId(int $ref_id): EventModel
	{
		$this->ref_id = $ref_id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParentRefId(): int
	{
		return (isset($this->parent_ref_id) ? $this->parent_ref_id : -1);
	}

	/**
	 * @param int $parent_ref_id
	 * @return EventModel
	 */
	public function setParentRefId(int $parent_ref_id): EventModel
	{
		$this->parent_ref_id = $parent_ref_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getObjType(): string
	{
		return (isset($this->obj_type) ? $this->obj_type : '');
	}

	/**
	 * @param string $obj_type
	 * @return EventModel
	 */
	public function setObjType(string $obj_type): EventModel
	{
		$this->obj_type = $obj_type;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUsrId(): int
	{
		return (isset($this->usr_id) ? $this->usr_id : -1);
	}

	/**
	 * @param int $usr_id
	 * @return EventModel
	 */
	public function setUsrId(int $usr_id): EventModel
	{
		$this->usr_id = $usr_id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRoleId(): int
	{
		return (isset($this->role_id) ? $this->role_id : -1);
	}

	/**
	 * @param int $role_id
	 * @return EventModel
	 */
	public function setRoleId(int $role_id): EventModel
	{
		$this->role_id = $role_id;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAppointments(): array
	{
		return (isset($this->appointments) ? $this->appointments : []);
	}

	/**
	 * @param array $appointments
	 * @return EventModel
	 */
	public function setAppointments(array $appointments): EventModel
	{
		$this->appointments = $appointments;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLpStatus(): string
	{
		return (isset($this->lp_status) ? $this->lp_status : '');
	}

	/**
	 * @param string $lp_status
	 * @return EventModel
	 */
	public function setLpStatus(string $lp_status): EventModel
	{
		$this->lp_status = $lp_status;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLpPercentage(): int
	{
		return (isset($this->lp_percentage) ? $this->lp_percentage : -1);
	}

	/**
	 * @param int $lp_percentage
	 * @return EventModel
	 */
	public function setLpPercentage(int $lp_percentage): EventModel
	{
		$this->lp_percentage = $lp_percentage;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEventName(): string
	{
		return (isset($this->event_name) ? $this->event_name : '');
	}

	/**
	 * @param string $event_name
	 * @return EventModel
	 */
	public function setEventName(string $event_name): EventModel
	{
		$this->event_name = $event_name;
		return $this;
	}

}