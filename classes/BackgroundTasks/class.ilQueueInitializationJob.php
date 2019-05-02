<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

use \ILIAS\BackgroundTasks\Implementation\Bucket\State;
use \ILIAS\BackgroundTasks\Implementation\Tasks\AbstractJob;
use \ILIAS\BackgroundTasks\Implementation\Values\ScalarValues\IntegerValue;
use \ILIAS\BackgroundTasks\Observer;
use \ILIAS\BackgroundTasks\Types\SingleType;
use \QU\LERQ\BackgroundTasks\QueueInitializationJobDefinition;
use \QU\LERQ\Events\AbstractEvent;

/**
 * Class ilQueueInitializationJob
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class ilQueueInitializationJob extends AbstractJob
{
	/** @var string */
	protected $db_table;

	/** @var QueueInitializationJobDefinition */
	protected $definitions;

	/** @var \ilDB */
	protected $db;

	/**
	 * @param array $input
	 * @param Observer $observer
	 * @return IntegerValue
	 * @throws \ILIAS\BackgroundTasks\Exceptions\InvalidArgumentException
	 */
	public function run(array $input, Observer $observer): IntegerValue
	{
		global $DIC;

		$this->logMessage('Start initial queue collection.');

		$this->db = $DIC->database();
		$this->definitions = new QueueInitializationJobDefinition();
		$this->db_table = $this->definitions::JOB_TABLE;

		$output = new IntegerValue();

		$task_info = $this->getTaskInformations();
		if(empty($task_info)) {
			$this->initTask();
		}
		if ($this->isTaskFinished($task_info)) {
			$this->logMessage('Queue consistency protection. Stopped task, because it is already finished.');
			$output->setValue($this->definitions::JOB_RETURN_SUCCESS);
			$observer->notifyState(State::FINISHED);
			return $output;
		}
		if ($this->isTaskLocked($task_info)) {
			// we should never get here if no fatal error occures
			$this->logMessage('Parallel execution protection. Stopped task, because it is locked.');
			$output->setValue($this->definitions::JOB_RETURN_LOCKED);
			$observer->notifyState(State::ERROR);
			return $output;
		}
		if ($this->isTaskRunning($task_info)) {
			$this->logMessage('Parallel execution protection. Stopped task, because it is already running.');
			$output->setValue($this->definitions::JOB_RETURN_ALREADY_RUNNING);
			return $output;
		}
		if (!$this->isQueueEmpty()) {
			$this->logMessage('Queue consistency protection. Stopped task, because queue is not empty.');
			$this->updateTask([
				'state' => $this->definitions::JOB_STATE_FAILED,
			]);
			$output->setValue($this->definitions::JOB_RETURN_FAILED);
			$observer->notifyState(State::ERROR);
			return $output;
		}

		$this->updateTask([
			'lock' => true,
			'state' => $this->definitions::JOB_STATE_RUNNING,
		]);
		$observer->notifyState(State::RUNNING);

		$found_items = 0;
		$observer->notifyPercentage($this, 0);
		try {
			// collect items to process
			$this->logMessage('Start collecting data.');

			$select_assignments = 'SELECT * FROM object_reference oref ' .
				'LEFT JOIN rbac_fa rfa ON rfa.parent = oref.ref_id ' .
				'LEFT JOIN rbac_ua rua ON rua.rol_id = rfa.rol_id ' .
				'WHERE rfa.assign = "y" ' .
				'AND rua.rol_id IS NOT NULL ';
			$res = $this->db->query($select_assignments);
			$assignments = [];
			$count = 0;
			while ($data = $this->db->fetchAssoc($res))
			{
				if ($data['usr_id'] == 6) {
					continue;
				}
				if (!array_key_exists($data['ref_id'], $assignments)) {
					$assignments[$data['ref_id']] = [];
				}
				$assignments[$data['ref_id']][$data['usr_id']] = [
					'obj_id' => $data['obj_id'],
					'rol_id' => $data['rol_id'],
				];
				$found_items++;
				$count++;
				if ($count > 100) {
					$observer->heartbeat();
					$count = 0;
				}
			}


			$this->updateTask(['found_items' => $found_items]);
			$this->logMessage('Found overall ' . $found_items . ' items.', 'debug');
			$this->logMessage('Finished collecting data.');

		} catch (\Exception $e) {
			$this->logMessage($e->getMessage(), 'error');
			$this->updateTask([
				'lock' => false,
				'found_items' => $found_items,
				'state' => $this->definitions::JOB_STATE_FAILED,
			]);
			$output->setValue((int)$e->getCode());
			return $output;
		}
		$observer->heartbeat();

		// if we get no items, the task is finished
		if ($found_items < 1) {
			$this->updateTask([
				'lock' => false,
				'state' => $this->definitions::JOB_STATE_FINISHED,
			]);
			$output->setValue($this->definitions::JOB_RETURN_SUCCESS);
			return $output;
		}

		try {
			// process items
			$this->logMessage('Start processing data.');

			$lp_handler = new \QU\LERQ\Events\LearningProgressEvent();
			$mem_handler = new \QU\LERQ\Events\MemberEvent();
			$processed_items = 0;

			foreach ($assignments as $ref_id => $odata) {
				$observer->heartbeat();

				foreach ($odata as $user_id => $udata) {
					$this->logMessage($ref_id . '(ref) => ' . $user_id . '(usr) => ' . var_export($udata, true));

					$mem_handler->handle_event('init_event_mem', [
						'obj_id' => $udata['obj_id'],
						'usr_id' => $user_id,
						'role_id' => $udata['rol_id'],
						'ref_id' => $ref_id
					]);

					$status = \ilLPStatus::_lookupStatus($udata['obj_id'], $user_id, false);
					if (isset($status)) {
						$lp_handler->handle_event('init_event_lp', [
							'obj_id' => $udata['obj_id'],
							'usr_id' => $user_id,
							'status' => $status,
							'percentage' => \ilLPStatus::_lookupPercentage($udata['obj_id'], $user_id),
							'ref_id' => $ref_id
						]);
					}
					$processed_items++;
				}

				$this->updateTask([
					'processed_items' => $processed_items,
					'progress' => $this->measureProgress($found_items, $processed_items),
				]);
				$observer->notifyPercentage($this, (int)$this->measureProgress($found_items, $processed_items));
			}

			$this->logMessage('Processed ' . $processed_items . ' items.', 'debug');
			$this->logMessage('Finished processing data.');


		} catch (\Exception $e) {
			$this->logMessage($e->getMessage(), 'error');
			$this->updateTask([
				'lock' => false,
				'found_items' => $found_items,
				'processed_items' => $processed_items,
				'progress' => $this->measureProgress($found_items, $processed_items),
				'state' => $this->definitions::JOB_STATE_FAILED,
			]);
			$output->setValue($this->definitions::JOB_RETURN_FAILED);
			return $output;
		}

		$this->updateTask([
			'lock' => false,
			'state' => $this->definitions::JOB_STATE_FINISHED,
			'progress' => $this->measureProgress($found_items, $processed_items),
			'finished_ts' => strtotime('now'),
		]);
		$this->logMessage('Finished initial queue collection.');
		$output->setValue($this->definitions::JOB_RETURN_SUCCESS);
		return $output;
	}

	/**
	 * @inheritDoc
	 */
	public function isStateless()
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getExpectedTimeOfTaskInSeconds()
	{
		return 100;
	}

	/**
	 * @inheritDoc
	 */
	public function getInputTypes()
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function getOutputType()
	{
		return new SingleType(IntegerValue::class);
	}

	/**
	 * @return array
	 */
	public function getTaskInformations(): array
	{
		global $DIC;

		$settings = $DIC->settings();

		$task_info = json_decode($settings->get($this->db_table, '{}'), true);
		if (empty($task_info)) {
			$task_info = $this->initTask();
		}

		return $task_info;
	}

	/**
	 * @return array
	 */
	protected function initTask(): array
	{
		global $DIC;
		$settings = $DIC->settings();

		$this->logMessage('Init Task for LpEventQueue Initialization.', 'debug');

		$task_info = [
			'lock' => false,
			'state' => $this->definitions::JOB_STATE_INIT,
			'found_items' => 0,
			'processed_items' => 0,
			'progress' => 0,
			'started_ts' => strtotime('now'),
			'finished_ts' => null,
		];

		$settings->set($this->db_table, json_encode($task_info));
		return $task_info;
	}

	/***
	 * @param array $data
	 * @return array
	 */
	protected function updateTask(array $data): array
	{
		global $DIC;
		$settings = $DIC->settings();


		$task_info = $this->getTaskInformations();

		if(array_key_exists('lock', $data)) {
			$task_info['lock'] = $data['lock'];
		}
		if(array_key_exists('state', $data)) {
			$task_info['state'] = $data['state'];
		}
		if(array_key_exists('found_items', $data)) {
			$task_info['found_items'] = $data['found_items'];
		}
		if(array_key_exists('processed_items', $data)) {
			$task_info['processed_items'] = $data['processed_items'];
		}
		if(array_key_exists('progress', $data)) {
			$task_info['progress'] = $data['progress'];
		}
		if(array_key_exists('finished_ts', $data)) {
			if(is_string($data['finished_ts'])) {
				$data['finished_ts'] = strtotime($data['finished_ts']);
			}
			$task_info['finished_ts'] = $data['finished_ts'];
		}


		$this->logMessage('Update data: ' . json_encode($task_info), 'debug');

		$settings->set($this->db_table, json_encode($task_info));
		return $task_info;
	}

	/**
	 * @param int $found
	 * @param int $processed
	 * @return float
	 */
	protected function measureProgress(int $found, int $processed = 0): float
	{
		return (float)(100 / $found * $processed);
	}

	/**
	 * @param string $message
	 * @param string $type
	 * @return void
	 */
	protected function logMessage(string $message, string $type = 'info')
	{
		global $DIC;

		$logger = $DIC->logger()->root();

		$m_prefix = '[BackgroundTask][LpEventReportingQueue] ';
		switch ($type) {
			case 'critical':
				$logger->critical($m_prefix . $message);
				break;
			case 'error':
				$logger->error($m_prefix . $message);
				break;
			case 'warning':
				$logger->warning($m_prefix . $message);
				break;
			case 'notice':
				$logger->notice($m_prefix . $message);
				break;
			case 'info':
				$logger->info($m_prefix . $message);
				break;
			case 'debug':
				$logger->debug($m_prefix . $message);
				break;
		}
	}

	/**
	 * @param $task_info
	 * @return bool
	 */
	protected function isTaskRunning($task_info): bool
	{
		return $task_info['state'] === $this->definitions::JOB_STATE_RUNNING;
	}

	/**
	 * @param $task_info
	 * @return bool
	 */
	protected function isTaskStopped($task_info): bool
	{
		return $task_info['state'] === $this->definitions::JOB_STATE_STOPPED;
	}

	/**
	 * @param $task_info
	 * @return bool
	 */
	protected function isTaskFinished($task_info): bool
	{
		return $task_info['state'] === $this->definitions::JOB_STATE_FINISHED;
	}

	/**
	 * @param $task_info
	 * @return bool
	 */
	protected function isTaskFailed($task_info): bool
	{
		return $task_info['state'] === $this->definitions::JOB_STATE_FAILED;
	}

	/**
	 * @param $task_info
	 * @return bool
	 */
	protected function isTaskLocked($task_info): bool
	{
		return $task_info['lock'] === true;
	}

	/**
	 * @return bool
	 */
	private function isQueueEmpty(): bool
	{
		$query = 'SELECT * FROM ' . AbstractEvent::DB_TABLE . ' LIMIT 1';
		$res = $this->db->query($query);
//		$queue = $this->db->fetchAll($res);

		return ($res->numRows() == 0);
	}

}