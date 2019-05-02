<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");
include_once("./Customizing/global/plugins/Services/Cron/CronHook/LpEventReportQueue/classes/BackgroundTasks/class.ilQueueInitialization.php");
include_once("./Customizing/global/plugins/Services/Cron/CronHook/LpEventReportQueue/classes/BackgroundTasks/class.ilQueueInitializationJob.php");

use \ILIAS\BackgroundTasks\Implementation\Bucket\BasicBucket;
use \QU\LERQ\BackgroundTasks\QueueInitializationJobDefinition;
use \QU\LERQ\Events\AbstractEvent;

class ilLpEventReportQueueConfigGUI extends ilPluginConfigGUI
{
	/** @var ilLpEventReportQueuePlugin */
	private $plugin;

	/** @var \ilCtrl */
	protected $ctrl;

	/** @var \ilLanguage */
	protected $lng;

	/** @var \ilTemplate */
	protected $tpl;

	/** @var \ilSetting */
	protected $settings;

	/** @var \ILIAS\DI\BackgroundTaskServices */
	protected $backgroundTasks = null;

	/**
	 * @return void
	 */
	public function construct()
	{
		global $DIC;

		$this->plugin = ilLpEventReportQueuePlugin::getInstance();
		$this->ctrl = $DIC->ctrl();
		$this->lng = $DIC->language();
		$this->tpl = $DIC["tpl"];
		$this->settings = $DIC->settings();
		if (null === $this->backgroundTasks) {
			$this->backgroundTasks = $DIC->backgroundTasks();
		}
	}

	function performCommand($cmd)
	{
		$this->construct();
		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class) {
			default:
				switch ($cmd) {
					case "configure":
						$this->configure();
						break;
					default:
						$cmd .= 'Cmd';
						$this->$cmd();
						break;
				}
				break;
		}
	}

	public function configure()
	{
		$form = new ilPropertyFormGUI();
		$form->setTitle($this->plugin->getPluginName());

		$task_info = json_decode($this->settings->get(QueueInitializationJobDefinition::JOB_TABLE, '{}'), true);
		if(!$this->wasInitializationStarted($task_info)) {
			$ne = new \ilNonEditableValueGUI('', 'start_initialization_by_click_first');
			$ne->setValue($this->plugin->txt('start_initialization_by_click_first'));
			$ne->setInfo(sprintf($this->plugin->txt('start_initialization_by_click_info'), $this->plugin->txt("start_initialization")));
			$form->addItem($ne);

			$form->addCommandButton("startInitialization", $this->plugin->txt("start_initialization"));

		} else if($this->canInitializationStart($task_info)) {
			$ne = new \ilNonEditableValueGUI('', 'start_initialization_by_click');
			$ne->setValue($this->plugin->txt('start_initialization_by_click'));
			$ne->setInfo(sprintf($this->plugin->txt('start_initialization_by_click_info'), $this->plugin->txt("start_initialization")));
			$form->addItem($ne);

			$form->addCommandButton("startInitialization", $this->plugin->txt("start_initialization"));

		}

		if($this->hasInitializationFailed($task_info) || $this->hasInitializationFinished($task_info)) {
			$ne = new \ilNonEditableValueGUI('', 'show_initialization_status');
			$ne->setValue($this->plugin->txt('show_initialization_status'));
			$ne->setInfo(sprintf($this->plugin->txt('show_initialization_status_info'), $task_info['state']));
			$form->addItem($ne);

			global $DIC;
			if ($DIC->user()->getId() == 6) {
				$form->addCommandButton("resetQueue", $this->lng->txt("reset"));
			}
		}


		$form->setFormAction($this->ctrl->getFormAction($this));
//		$startLink = $this->ctrl->getLinkTarget($this, 'startInitialization', false, true, false);

		$this->tpl->setContent($form->getHTML());
//		$this->tpl->setContent('<a href="' . $startLink . '">' . $this->plugin->txt("start_initialization") . '</a>');
	}

	public function startInitializationCmd(): string
	{
		global $DIC;

		$factory = $this->backgroundTasks->taskFactory();
		$taskManager = $this->backgroundTasks->taskManager();

		$bucket = new BasicBucket();
		$bucket->setUserId($DIC->user()->getId());
		$task = $factory->createTask(ilQueueInitializationJob::class);

		$interaction = ilQueueInitialization::class;
		$queueinit_interaction = $factory->createTask($interaction, [
			$task
		]);

		$bucket->setTask($queueinit_interaction);
		$bucket->setTitle($this->plugin->txt('queue_initialization'));
		$bucket->setDescription($this->plugin->txt('queue_initialization_info'));

		$taskManager->run($bucket);

		\ilUtil::sendInfo($this->plugin->txt('queue_initialization_confirm_started'), true);
		$this->ctrl->redirect($this, 'configure');
		return;

	}

	public function resetQueueCmd()
	{
		$this->plugin->deactivate();

		global $DIC;
		$DIC->database()->manipulate('DELETE FROM ' . AbstractEvent::DB_TABLE . ' WHERE true;');
		$this->settings->set(QueueInitializationJobDefinition::JOB_TABLE, '{}');

		$this->plugin->activate();

		\ilUtil::sendInfo($this->plugin->txt('queue_reset_confirm'), true);
		$this->ctrl->redirect($this, 'configure');
		return;
	}

	/**
	 * @param array $task_info
	 * @return bool
	 */
	public function wasInitializationStarted($task_info = []): bool
	{
		return (
			!empty($task_info) &&
			$task_info['state'] !== QueueInitializationJobDefinition::JOB_STATE_INIT
		);
	}

	/**
	 * @param array $task_info
	 * @return bool
	 */
	public function canInitializationStart($task_info = []): bool
	{
		return (
			empty($task_info) ||
			!in_array($task_info['state'], [
				QueueInitializationJobDefinition::JOB_STATE_FINISHED,
				QueueInitializationJobDefinition::JOB_STATE_RUNNING,
				QueueInitializationJobDefinition::JOB_STATE_STARTED
			]));
	}

	/**
	 * @param array $task_info
	 * @return bool
	 */
	public function hasInitializationFailed($task_info = []): bool
	{
		return $task_info['state'] === QueueInitializationJobDefinition::JOB_STATE_FAILED;
	}

	/**
	 * @param array $task_info
	 * @return bool
	 */
	public function hasInitializationFinished($task_info = []): bool
	{
		return $task_info['state'] === QueueInitializationJobDefinition::JOB_STATE_FINISHED;
	}

}