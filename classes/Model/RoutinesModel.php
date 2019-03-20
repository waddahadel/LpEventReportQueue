<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Model;

class RoutinesModel
{
	/** @var bool */
	private $collectUserData;
	/** @var bool */
	private $collectUDFData;
	/** @var bool */
	private $collectMemberData;
	/** @var bool */
	private $collectLpPeriod;

	/**
	 * @return bool
	 */
	public function getCollectUserData(): bool
	{
		return ($this->collectUserData == true);
	}

	/**
	 * @param bool $collectUserData
	 */
	public function setCollectUserData(bool $collectUserData)
	{
		$this->collectUserData = $collectUserData;
	}

	/**
	 * @return bool
	 */
	public function getCollectUDFData(): bool
	{
		return ($this->collectUDFData == true);
	}

	/**
	 * @param bool $collectUDFData
	 */
	public function setCollectUDFData(bool $collectUDFData)
	{
		$this->collectUDFData = $collectUDFData;
	}

	/**
	 * @return bool
	 */
	public function getCollectMemberData(): bool
	{
		return ($this->collectMemberData == true);
	}

	/**
	 * @param bool $collectMemberData
	 */
	public function setCollectMemberData(bool $collectMemberData)
	{
		$this->collectMemberData = $collectMemberData;
	}

	/**
	 * @return bool
	 */
	public function getCollectLpPeriod(): bool
	{
		return ($this->collectLpPeriod == true);
	}

	/**
	 * @param bool $collectLpPeriod
	 */
	public function setCollectLpPeriod(bool $collectLpPeriod)
	{
		$this->collectLpPeriod = $collectLpPeriod;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return json_encode([
			'collectUserData' => $this->getCollectUserData(),
			'collectUDFData' => $this->getCollectUDFData(),
			'collectMemberData' => $this->getCollectMemberData(),
			'collectLpPeriod' => $this->getCollectLpPeriod(),
		]);
	}

}