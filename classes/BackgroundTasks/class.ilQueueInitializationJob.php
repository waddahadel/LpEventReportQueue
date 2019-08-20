<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

use \ILIAS\BackgroundTasks\Implementation\Bucket\State;
use \ILIAS\BackgroundTasks\Implementation\Tasks\AbstractJob;
use \ILIAS\BackgroundTasks\Implementation\Values\ScalarValues\IntegerValue;
use \ILIAS\BackgroundTasks\Observer;
use \ILIAS\BackgroundTasks\Types\SingleType;
use \QU\LERQ\BackgroundTasks\QueueInitializationJobDefinition;
use \QU\LERQ\Events\AbstractEvent;
use \QU\LERQ\BackgroundTasks\AssignmentCollector;
use \QU\LERQ\Events\LearningProgressEvent;
use \QU\LERQ\Events\MemberEvent;

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

	/** @var AssignmentCollector */
	protected $collector;

	/**
	 * @param array $input
	 * @param Observer $observer
	 * @return IntegerValue
	 * @throws \ILIAS\BackgroundTasks\Exceptions\InvalidArgumentException
	 */
	public function run(array $input, Observer $observer): IntegerValue
	{
		global $DIC;

		\ilPluginAdmin::getPluginObject(
			"Services",
			"Cron",
			"crnhk",
			"LpEventReportQueue"
		);

		$this->logMessage('Start initial queue collection.');

		$this->db = $DIC->database();
		$this->definitions = new QueueInitializationJobDefinition();
		$this->db_table = $this->definitions::JOB_TABLE;

		$output = new IntegerValue();

		/* check if task can run */
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

		/* start task */
		$this->updateTask([
			'lock' => true,
			'state' => $this->definitions::JOB_STATE_RUNNING,
		]);
		$observer->notifyState(State::RUNNING);
		$observer->notifyPercentage($this, 0);

		try {
			// collect items to process
			$this->logMessage('Start collecting data.');

			/* prepare to get data */
			$this->collector = new AssignmentCollector($this->db);
			$found_items = $this->collector->getCountOfAllAssignments();
			$this->updateTask(['found_items' => $found_items]);
			$this->logMessage('Found overall ' . $found_items . ' items.', 'debug');
			if ($found_items < 1) {
				$this->updateTask([
					'lock' => false,
					'state' => $this->definitions::JOB_STATE_FINISHED,
				]);
				$output->setValue($this->definitions::JOB_RETURN_SUCCESS);
				return $output;
			}

			/* prepare vars */
			$limit_per_run = 1000;
			$start_ref_id = 0;
			$processed_items = 0;
			$run = 1;
			$assignments = [];

			/* collect and process items */
			while ($processed_items < $found_items) {
				/* collect */
				$assignments = $this->collectData($limit_per_run, $start_ref_id);
				$this->logMessage('Prepared ' . $this->collector->countAssignmentItems($assignments) . ' items for run #' . $run . '.', 'debug');
				if (($processed_items + count($assignments)) == $found_items) {
					$this->logMessage('Finished collecting data.');
				}

				/* process */
				$processed = $this->processData($observer, $assignments);
				$processed_items += $processed['processed'];
				$start_ref_id = ($processed['last_ref'] + 1);
				$this->logMessage('Processed ' . $processed['processed'] . ' items for run #' . $run . '.', 'debug');
				unset($processed);

				/* update task informations */
				$this->updateTask([
					'progress' => $this->measureProgress($found_items, $processed_items),
				]);
				$observer->notifyPercentage($this, (int)$this->measureProgress($found_items, $processed_items));
			}
			$this->logMessage('Finished processing data.');

		} catch (\Exception $e) {
			$this->logMessage($e->getMessage(), 'error');
			$this->updateTask([
				'lock' => false,
				'state' => $this->definitions::JOB_STATE_FAILED,
			]);
			$output->setValue((int)$e->getCode());
			return $output;
		}


		$observer->heartbeat();
		$this->updateTask([
			'lock' => false,
			'state' => $this->definitions::JOB_STATE_FINISHED,
//			'progress' => 100,
			'finished_ts' => strtotime('now'),
		]);
		$this->logMessage('Finished initial queue collection.');
		$observer->notifyPercentage($this, 100);
		$observer->notifyState(State::FINISHED);

		$output->setValue($this->definitions::JOB_RETURN_SUCCESS);
		return $output;
	}

	/**
	 * @param int $limit
	 * @param int $start_ref
	 * @return array
	 */
	protected function collectData(int $limit = 1000, int $start_ref = 0): array
	{
		return $this->collector->getAssignments($limit, $start_ref);
	}

	/**
	 * @param Observer $observer
	 * @param array $assignments
	 * @return array
	 */
	protected function processData(Observer $observer, array $assignments): array
	{

		$lp_handler = new LearningProgressEvent();
		$mem_handler = new MemberEvent();
		$processed_items = ($this->getTaskInformations()['processed_items'] * 1);

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
			$last_ref = $ref_id;
			$this->updateTask([
				'processed_items' => $processed_items,
				'last_item' => $last_ref,
			]);
		}

		return ['processed' => $processed_items, 'last_ref' => $last_ref];
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
			'last_item' => 0,
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
		if(array_key_exists('last_item', $data)) {
			$task_info['last_item'] = $data['last_item'];
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