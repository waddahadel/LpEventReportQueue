<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API;

/**
 * Interface QueueInterface
 * @package QU\LERQ\API
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
interface QueueInterface
{
	/**
	 * Register a provider plugin
	 *
	 * @param string $name			Plugin Name
	 * @param string $namespace		Plugin Namespace
	 * @param string $path			Plugin root path (realpath)
	 * @param bool $hasOverrides	Does the plugin has overrides? (default: false)
	 * @return bool
	 */
	public function registerProvider(string $name, string $namespace, string $path, bool $hasOverrides = false): bool;

	// @ToDo: create a filter object
	public function createFilterObject();

	// @ToDo: Get a collection!
	// @ToDo: Create an Interface?!
	public function getCollection();
}