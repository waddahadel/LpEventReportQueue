<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Events;

use QU\LERQ\Model\MemberModel;
use QU\LERQ\Model\ObjectModel;
use QU\LERQ\Model\QueueModel;
use QU\LERQ\Model\SettingsModel;
use QU\LERQ\Model\UserModel;

/**
 * Class AbstractEvent
 * @package QU\LERQ\Events
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
abstract class AbstractEvent implements EventInterface
{
	const DB_TABLE = 'lerq_queue';

	/** @var \ilLog */
	protected $logger;

	/** @var \ilDB */
	protected $database;

	/** @var \ilIniFile */
	protected $configInstance;

	/**
	 * AbstractEvent constructor.
	 */
	public function __construct()
	{
		global $DIC;

		$this->logger = $DIC->logger();
		$this->database = $DIC->database();
	}

	protected function save(array $data)
	{
		try {
			$queue = new QueueModel();
			$settings = new SettingsModel();

			$queue->setTimestamp($data['timestamp'])
				->setEvent($data['event'])
				->setEventType($this->mapEventToType($data['event']))
				->setProgress($data['progress'])
				->setAssignment($data['assignment']);

			if (array_key_exists('lpperiod', $data) && !empty($data['lpperiod'])) {
				/**
				 * @var \ilDateTime[] $lpp
				 */
				$lpp = $data['lpperiod'];
				$queue->setCourseStart($lpp['course_start']->getUnixTime());
				$queue->setCourseEnd($lpp['course_end']->getUnixTime());
			}

			$user = new UserModel();
			if ($settings->getItem('user_fields')) {
				if (array_key_exists('userdata', $data) && !empty($data['userdata'])) {
					$ud = $data['userdata'];
					if ($settings->getItem('user_id'))
						$user->setUsrId($ud['user_id']);
					if ($settings->getItem('login'))
						$user->setLogin($ud['username']);
					if ($settings->getItem('firstname'))
						$user->setFirstname($ud['firstname']);
					if ($settings->getItem('lastname'))
						$user->setLastname($ud['lastname']);
					if ($settings->getItem('title'))
						$user->setTitle($ud['title']);
					if ($settings->getItem('gender'))
						$user->setGender($ud['gender']);
					if ($settings->getItem('email'))
						$user->setEmail($ud['email']);
					if ($settings->getItem('institution'))
						$user->setInstitution($ud['institution']);
					if ($settings->getItem('street'))
						$user->setStreet($ud['street']);
					if ($settings->getItem('city'))
						$user->setCity($ud['city']);
					if ($settings->getItem('country'))
						$user->setCountry($ud['country']);
					if ($settings->getItem('phone_office'))
						$user->setPhoneOffice($ud['phone_office']);
					if ($settings->getItem('hobby'))
						$user->setHobby($ud['hobby']);
					if ($settings->getItem('department'))
						$user->setDepartment($ud['department']);
					if ($settings->getItem('phone_home'))
						$user->setPhoneHome($ud['phone_home']);
					if ($settings->getItem('phone_mobile'))
						$user->setPhoneMobile($ud['phone_mobile']);
					if ($settings->getItem('phone_fax'))
						$user->setFax($ud['phone_fax']);
					if ($settings->getItem('referral_comment'))
						$user->setReferralComment($ud['referral_comment']);
					if ($settings->getItem('matriculation'))
						$user->setMatriculation($ud['matriculation']);
					if ($settings->getItem('active'))
						$user->setActive($ud['active']);
					if ($settings->getItem('approval_date'))
						$user->setApprovalDate($ud['approval_date']);
					if ($settings->getItem('agree_date'))
						$user->setAgreeDate($ud['agree_date']);
					if ($settings->getItem('auth_mode'))
						$user->setAuthMode($ud['auth_mode']);
					if ($settings->getItem('ext_account'))
						$user->setExtAccount($ud['ext_account']);
					if ($settings->getItem('birthday'))
						$user->setBirthday($ud['birthday']);
					if ($settings->getItem('import_id'))
						$user->setImportId($ud['import_id']);
					if (array_key_exists('udfdata', $data) && !empty($data['udfdata'])) {
						if ($settings->getItem('udf_fields')) {
							$user->setUdfData($data['udfdata']);
						}
					}
				}
			}
			$queue->setUserData($user);

			$object = new ObjectModel();
			if (array_key_exists('objectdata', $data) && !empty($data['objectdata'])) {
				if (
					$settings->getItem('obj_select') === '*' ||
					$settings->getItem('obj_select') == $data['objectdata']['type']
				) {
					$od = $data['objectdata'];
					$object->setTitle($od['title'])
						->setId($od['id'])
						->setRefId($od['ref_id'])
						->setLink($od['link'])
						->setType($od['type'])
						->setCourseTitle($od['course_title'])
						->setCourseId($od['course_id'])
						->setCourseRefId($od['course_ref_id']);
				}
			}
			$queue->setObjData($object);

			$member = new MemberModel();
			if (array_key_exists('memberdata', $data) && !empty($data['memberdata'])) {
				$md = $data['memberdata'];
				$member->setMemberRole($md['role'])
					->setCourseTitle($md['course_title'])
					->setCourseId($md['course_id'])
					->setCourseRefId($md['course_ref_id']);
			}
			$queue->setMemData($member);

			$this->_saveEventData($queue);
			return true;

		} catch (\Exception $e) {
			// @Todo Exception
			return false;
		}
	}

	protected function mapEventToType(string $a_event)
	{
		$map = [
			"init_event_lp" => 'lp_event',
			"init_event_mem" => 'member_event',
//			"" => "lp_event",
			"updateStatus" => "lp_event",
//			"" => "member_event",
			"addParticipant" => "member_event",
			"deleteParticipant" => "member_event",
			"addToWaitingList" => "member_event",
			"removeFromWaitingList" => "member_event",
			"createAssignment" => "member_event",
			"deleteAssignment" => "member_event",
			"updateAssignment" => "member_event",
//			"" => "object_event",
			"create" => "object_event",
			"delete" => "object_event",
			"toTrash" => "object_event",
			"undelete" => "object_event",
			"update" => "object_event",
			"putObjectInTree" => "object_event",
		];
		return $map[$a_event];
	}

	private function _saveEventData(QueueModel $queueModel)
	{
		$insert = 'INSERT INTO `' . self::DB_TABLE . '` ';
		$insert .= '(`id`, `timestamp`, `event`, `event_type`, `progress`, `assignment`, ';
		$insert .= '`course_start`, `course_end`, `user_data`, `obj_data`, `mem_data`) ';
		$insert .= 'VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s); ';

		$types = [
			'integer',
			'integer',
			'text',
			'text',
			'text',
			'text',
			'integer',
			'integer',
			'text',
			'text',
			'text',
		];

		$values = [
			$this->database->nextId(self::DB_TABLE),
			$queueModel->getTimestamp(false),
			$queueModel->getEvent(),
			$queueModel->getEventType(),
			$queueModel->getProgress(),
			$queueModel->getAssignment(),
			$queueModel->getCourseStart(false),
			$queueModel->getCourseEnd(false),
			$queueModel->getUserData()->__toString(),
			$queueModel->getObjData()->__toString(),
			$queueModel->getMemData()->__toString(),
		];


		$this->database->manipulateF(
			$insert,
			$types,
			$values
		);
	}

}