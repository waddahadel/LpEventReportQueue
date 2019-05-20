<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Events;

use QU\LERQ\Model\EventModel;
use QU\LERQ\Queue\Processor;

/**
 * Class MemberEvent
 * @package QU\LERQ\Events
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class MemberEvent extends AbstractEvent implements EventInterface
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
			->setUsrId($a_params['usr_id'])
			->setEventName($a_event);
		if (isset($a_params['role_id'])) {
			$event->setRoleId($a_params['role_id']);
		}
		if (isset($a_params['ref_id'])) {
			$event->setRefId($a_params['ref_id']);
		}

		$data = $processor->capture($event);
		$data['timestamp'] = time();
		$data['event'] = $a_event;
		$data['progress'] = NULL;
		$data['assignment'] = (isset($a_params['role_id']) ? $this->mapAssignment($a_params['role_id']) : NULL);

		return $this->save($data);
	}

	/**
	 * @param int $status
	 * @return string
	 */
	private function mapAssignment(int $assignment_id): string
	{
		/** @var \ilObjRole $roleObj */
		$roleObj = \ilObjectFactory::getInstanceByObjId($assignment_id);

		return $roleObj->getTitle();
	}

}