<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Events;

use QU\LERQ\Model\EventModel;
use QU\LERQ\Queue\Processor;

/**
 * Class ObjectEvent
 * @package QU\LERQ\Events
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class ObjectEvent extends AbstractEvent implements EventInterface
{
	/** @var \ilLog */
	protected $logger;

	/** @var \ilDB */
	protected $database;

	/** @var \ilIniFile */
	protected $configInstance;

	/**
	 * @param string $a_event
	 * @param array $a_params
	 * @return bool
	 */
	public function handle_event(string $a_event, array $a_params): bool
	{
		$processor = new Processor();
		$event = new EventModel();

		$event->setObjId($a_params['obj_id'])
			->setEventName($a_event);
		if (isset($a_params['ref_id'])) {
			$event->setRefId($a_params['ref_id']);
		}
		if (isset($a_params['obj_type'])) {
			$event->setObjType($a_params['obj_type']);
		} else if ($a_params['type']) {
			$event->setObjType($a_params['type']);
		}
		if (isset($a_params['old_parent_ref_id'])) {
			$event->setParentRefId($a_params['old_parent_ref_id']);
		} else if (isset($a_params['parent_ref_id'])) {
			$event->setParentRefId($a_params['parent_ref_id']);
		}

		$data = $processor->capture($event);
		$data['timestamp'] = time();
		$data['event'] = $a_event;
		$data['progress'] = NULL;
		$data['assignment'] = NULL;

		return $this->save($data);
	}

}