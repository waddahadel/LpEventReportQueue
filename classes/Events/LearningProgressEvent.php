<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */
namespace QU\LERQ\Events;

include_once './Services/Tracking/classes/class.ilLPStatus.php';

use QU\LERQ\Model\EventModel;
use QU\LERQ\Queue\Processor;

/**
 * Class LearningProgressEvent
 * @package QU\LERQ\Events
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class LearningProgressEvent extends AbstractEvent implements EventInterface
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
			->setLpStatus($a_params['status'])
			->setLpPercentage($a_params['percentage'])
			->setEventName($a_event);
		if (isset($a_params['ref_id'])) {
			$event->setRefId($a_params['ref_id']);
		}

		$data = $processor->capture($event);
		$data['timestamp'] = time();
		$data['event'] = $this->mapInitEvent($a_event);
		$data['progress'] = $this->mapLpStatus($a_params['status']);
		$data['assignment'] = NULL;

		return $this->save($data);
	}

	/**
	 * @param int $status
	 * @return string
	 */
	private function mapLpStatus(int $status): string
	{
		$lpStatus = '';
		switch ($status) {
			case \ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM:
				$lpStatus = 'no_attempted';
				break;
			case \ilLPStatus::LP_STATUS_IN_PROGRESS_NUM:
				$lpStatus = 'in_progress';
				break;
			case \ilLPStatus::LP_STATUS_COMPLETED_NUM:
				$lpStatus = 'completed';
				break;
			case \ilLPStatus::LP_STATUS_FAILED_NUM:
				$lpStatus = 'failed';
				break;
		}
		return $lpStatus;
	}

}