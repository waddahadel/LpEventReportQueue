<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Events;

use QU\LERQ\Model\MemberModel;
use QU\LERQ\Model\ObjectModel;
use QU\LERQ\Model\QueueModel;
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

	protected function save(array $data)
	{
		try {
			$queue = new QueueModel();

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
			if (array_key_exists('userdata', $data) && !empty($data['userdata'])) {
				$ud = $data['userdata'];
				$user->setUsrId($ud['user_id'])
					->setLogin($ud['username'])
					->setFirstname($ud['firstname'])
					->setLastname($ud['lastname'])
					->setTitle($ud['title'])
					->setGender($ud['gender'])
					->setEmail($ud['email'])
					->setInstitution($ud['institution'])
					->setStreet($ud['street'])
					->setCity($ud['city'])
					->setCountry($ud['country'])
					->setPhoneOffice($ud['phone_office'])
					->setHobby($ud['hobby'])
					->setPhoneHome($ud['phone_home'])
					->setPhoneMobile($ud['phone_mobile'])
					->setFax($ud['phone_fax'])
					->setReferralComment($ud['referral_comment'])
					->setMatriculation($ud['matriculation'])
					->setActive($ud['active'])
					->setApprovalDate($ud['approval_date'])
					->setAgreeDate($ud['agree_date'])
					->setAuthMode($ud['auth_mode'])
					->setExtAccount($ud['ext_account'])
					->setBirthday($ud['birthday']);
				if (array_key_exists('udfdata', $data) && !empty($data['udfdata'])) {
					$user->setUdfData($data['udfdata']);
				}
			}
			$queue->setUserData($user);

			$object = new ObjectModel();
			if (array_key_exists('objectdata', $data) && !empty($data['objectdata'])) {
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