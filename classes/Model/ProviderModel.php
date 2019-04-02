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
	 * @return ProviderModel
	 */
	public function setName(string $name): ProviderModel
	{
		$this->name = $name;
		return $this;
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
	 * @return ProviderModel
	 */
	public function setNamespace(string $namespace): ProviderModel
	{
		$this->namespace = $namespace;
		return $this;
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
	 * @return ProviderModel
	 */
	public function setPath(string $path): ProviderModel
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getHasOverrides(): bool
	{
		return (isset($this->hasOverrides) ? $this->hasOverrides : false);
	}

	/**
	 * @param bool $hasOverrides
	 * @return ProviderModel
	 */
	public function setHasOverrides(bool $hasOverrides): ProviderModel
	{
		$this->hasOverrides = $hasOverrides;
		return $this;
	}

	/**
	 * @return RoutinesModel
	 */
	public function getActiveOverrides(): RoutinesModel
	{
		return (isset($this->activeOverrides) ? $this->activeOverrides : new RoutinesModel());
	}

	/**
	 * @param RoutinesModel $activeOverrides
	 * @return ProviderModel
	 */
	public function setActiveOverrides(RoutinesModel $activeOverrides): ProviderModel
	{
		$this->activeOverrides = $activeOverrides;
		return $this;
	}

}