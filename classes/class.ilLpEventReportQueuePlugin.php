<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("./Services/Cron/classes/class.ilCronHookPlugin.php");
require_once ('Autoload/LCAutoloader.php');

/**
 * Class ilLpEventReportQueuePlugin
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class ilLpEventReportQueuePlugin extends \ilCronHookPlugin
{
	const PLUGIN_ID = "lpeventreportqueue";
	const PLUGIN_NAME = "ilLpEventReportQueue";
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
		self::registerAutoloader();
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
			case "Services/User":
				switch($a_event)
				{
					case "afterCreate": // @Todo can we check if this is triggered by the registration formular?
						$handler = new \QU\LERQ\Events\LearningProgressEvent();
						$handler->handle_event($a_params);
						break;
				}
				break;
		}

		return true;
	}

}