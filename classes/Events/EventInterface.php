<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Events;

/**
 * Interface EventInterface
 * @package QU\LERQ\Events
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
interface EventInterface
{
	/**
	 * @param string $a_event
	 * @param array $a_params
	 * @return bool
	 */
	public function handle_event(string $a_event, array $a_params): bool;
}