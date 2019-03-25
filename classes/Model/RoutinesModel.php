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
		return $this->collectUserData;
	}

	/**
	 * @param bool $collectUserData
	 * @return RoutinesModel
	 */
	public function setCollectUserData(bool $collectUserData): RoutinesModel
	{
		$this->collectUserData = $collectUserData;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getCollectUDFData(): bool
	{
		return $this->collectUDFData;
	}

	/**
	 * @param bool $collectUDFData
	 * @return RoutinesModel
	 */
	public function setCollectUDFData(bool $collectUDFData): RoutinesModel
	{
		$this->collectUDFData = $collectUDFData;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getCollectMemberData(): bool
	{
		return $this->collectMemberData;
	}

	/**
	 * @param bool $collectMemberData
	 * @return RoutinesModel
	 */
	public function setCollectMemberData(bool $collectMemberData): RoutinesModel
	{
		$this->collectMemberData = $collectMemberData;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getCollectLpPeriod(): bool
	{
		return $this->collectLpPeriod;
	}

	/**
	 * @param bool $collectLpPeriod
	 * @return RoutinesModel
	 */
	public function setCollectLpPeriod(bool $collectLpPeriod): RoutinesModel
	{
		$this->collectLpPeriod = $collectLpPeriod;
		return $this;
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