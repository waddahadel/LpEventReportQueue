<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Model;

class ProviderModel
{
	/** @var string */
	private $name;
	/** @var string */
	private $namespace;
	/** @var string */
	private $path;
	/** @var bool */
	private $hasOverrides;
	/** @var RoutinesModel */
	private $activeOverrides;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getNamespace(): string
	{
		return $this->namespace;
	}

	/**
	 * @param string $namespace
	 */
	public function setNamespace(string $namespace)
	{
		$this->namespace = $namespace;
	}

	/**
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath(string $path)
	{
		$this->path = $path;
	}

	/**
	 * @return bool
	 */
	public function getHasOverrides(): bool
	{
		return $this->hasOverrides;
	}

	/**
	 * @param bool $hasOverrides
	 */
	public function setHasOverrides(bool $hasOverrides)
	{
		$this->hasOverrides = $hasOverrides;
	}

	/**
	 * @return RoutinesModel
	 */
	public function getActiveOverrides(): RoutinesModel
	{
		return $this->activeOverrides;
	}

	/**
	 * @param RoutinesModel $activeOverrides
	 */
	public function setActiveOverrides(RoutinesModel $activeOverrides)
	{
		$this->activeOverrides = $activeOverrides;
	}

}