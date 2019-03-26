<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("./Services/Cron/classes/class.ilCronHookPlugin.php");

/**
 * Class ilLpEventReportQueuePlugin
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class ilLpEventReportQueuePlugin extends \ilCronHookPlugin
{
	const PLUGIN_ID = "lpeventreportqueue";
	const PLUGIN_NAME = "LpEventReportQueue";
	const PLUGIN_SETTINGS = "qu_crnhk_lerq";
	const PLUGIN_NS = 'QU\LERQ';

	/** @var ilLpEventReportQueuePlugin */
	protected static $instance;

	/** @var \ilSetting */
	protected $settings;

	/** @var array */
	protected $jobs;

	/**
	 * @return void
	 */
	protected function init()
	{
		self::registerAPI();
		$this->jobs = $this->getCronJobInstances();
	}

	/**
	 * @return ilLpEventReportQueuePlugin
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return void
	 */
	public static function registerAutoloader()
	{
		global $DIC;

		if(!isset($DIC['autoload.lc.lcautoloader'])) {
			require_once(realpath(dirname(__FILE__)) . '/Autoload/LCAutoloader.php');
			$Autoloader = new LCAutoloader();
			$Autoloader->register();
			$Autoloader->addNamespace('ILIAS\Plugin', '/Customizing/global/plugins');
			$DIC['autoload.lc.lcautoloader'] = $Autoloader;
		}
		$DIC['autoload.lc.lcautoloader']->addNamespace(self::PLUGIN_NS, realpath(dirname(__FILE__)));
	}

	public static function registerAPI()
	{
		global $DIC;

		self::registerAutoloader();
		if(!isset($DIC['qu.lerq.api'])) {
			$api = new \QU\LERQ\API\API();
			$DIC['qu.lerq.api'] = $api;
		}
	}

	/**
	 * ilLpEventReportQueuePlugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
		$this->settings = new ilSetting(self::PLUGIN_SETTINGS);
	}

	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}

	/**
	 * @return \ilSetting
	 */
	public function getSettings(): ilSetting
	{
		return $this->settings;
	}

	/**
	 * @return array
	 */
	function getCronJobInstances()
	{
		// get array with all jobs
		$this->jobs = [];
//		$job = new \QUALITUS\EMH\Jobs\ExportExerciseUserData();
//		$this->jobs[$job->getId()] = $job;
		return $this->jobs;
	}

	/**
	 * @param $a_job_id
	 * @return mixed
	 * @throws Exception
	 */
	function getCronJobInstance($a_job_id)
	{
		// get specific job by id
		if(array_key_exists($a_job_id, $this->jobs)) {
			return $this->jobs[$a_job_id];
		}
		\ilUtil::sendFailure('ERROR: Unknown job called: ' . $a_job_id, true);
		return [];
	}

	protected function afterActivation() {
		// Do something
	}

	protected function afterDeactivation() {
		// Do something
	}

	protected function beforeUninstall() {
		// Do something
		return true;
	}

	/**
	 * @param string $a_component
	 * @param string $a_event
	 * @param array $a_params
	 * @return bool
	 */
	public function handleEvent($a_component, $a_event, $a_params)
	{
		switch($a_component)
		{
			case "Modules/Course":
				$this->debuglog($a_component, $a_event, $a_params);
				/*
				 * $a_event: addParticipant
				 * $a_params: ['obj_id', 'usr_id', 'role_id']
				 */
				/*
				 * $a_event: deleteParticipant
				 * $a_params: ['objid', 'usr_id']
				 */
				/*
				 * $a_event: addSubscriber
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: addToWaitingList
				 * $a_params: ['obj_id', 'usr_id']
				 */
				/*
				 * $a_event: create
				 * $a_params: ['object'(ilObjCourse), 'obj_id', 'appointments'(array)]
				 */
				/*
				 * $a_event: delete
				 * $a_params: ['object'(ilObjCourse), 'obj_id', 'appointments'(array)]
				 */
				/*
				 * $a_event: update
				 * $a_params: ['object'(ilObjCourse), 'obj_id', 'appointments'(array)]
				 */
				/*
				 * $a_event: removeFromWaitingList
				 * $a_params: ['obj_id', 'usr_id']
				 */
				break;
			case "Modules/Excercise":
				$this->debuglog($a_component, $a_event, $a_params);
				/*
				 * $a_event: createAssignment
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: delete
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: deleteAssignment
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: updateAssignment
				 * $a_params: [] // @Todo
				 */
				break;
			case "Modules/StudyProgramme":
				$this->debuglog($a_component, $a_event, $a_params);
				/*
				 * $a_event: userAssigned
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: userDeassigned
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: userSuccessful
				 * $a_params: [] // @Todo
				 */
				break;
			case "Services/Object":
				$this->debuglog($a_component, $a_event, $a_params);
				/*
				 * $a_event: create
				 * $a_params: ['obj_id', 'obj_type']
				 */
				/*
				 * $a_event: delete
				 * $a_params: ['obj_id', 'ref_id', 'type', 'old_parent_ref_id']
				 */
				/*
				 * $a_event: toTrash
				 * $a_params: ['obj_id', 'ref_id', 'old_parent_ref_id']
				 */
				/*
				 * $a_event: undelete
				 * $a_params: ['obj_id', 'ref_id']
				 */
				/*
				 * $a_event: update
				 * $a_params: ['obj_id', 'obj_type', 'ref_id']
				 */
				/*
				 * $a_event: putObjectInTree
				 * $a_params: ['object'(ilObjCourse), 'obj_type', 'obj_id', 'parent_ref_id']
				 */
				/*
				 * $a_event: beforeDeletion
				 * $a_params: ['object'(any object)]
				 */
				break;
			case "Services/Tracking":
				$this->debuglog($a_component, $a_event, $a_params);
				/*
				 * $a_event: updateStatus
				 * $a_params: ['obj_id', 'usr_id', 'status', 'percentage']
				 */
				break;
			case "Services/User":
				$this->debuglog($a_component, $a_event, $a_params);
				/*
				 * $a_event: afterCreate
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: afterUpdate
				 * $a_params: [] // @Todo
				 */
				/*
				 * $a_event: deleteUser
				 * $a_params: [] // @Todo
				 */
				break;
//			case "Services/User":
//				switch($a_event)
//				{
//					case "afterCreate":
//						$handler = new \QU\LERQ\Events\LearningProgressEvent();
//						$handler->handle_event($a_params);
//						break;
//				}
//				break;
		}

		return true;
	}

	private function debuglog($a_component, $a_event, $a_params)
	{
		global $DIC;
		$dumper = new \QU\LERQ\Helper\TVarDumper();
		$DIC->logger()->root()->debug(implode(' -> ', [
			var_export($a_component, true),
			var_export($a_event, true),
			$dumper::dump($a_params, 2),
		]));
	}

}