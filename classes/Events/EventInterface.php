<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Events;

interface EventInterface
{
	public function handle_event(string $a_event, array $a_params): bool;
}