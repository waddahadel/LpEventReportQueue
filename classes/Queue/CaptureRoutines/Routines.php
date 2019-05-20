<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Queue\CaptureRoutines;

use QU\LERQ\API\DataCaptureRoutinesInterface;
use QU\LERQ\Model\EventModel;

class Routines implements DataCaptureRoutinesInterface
{

	public function getAvailableOverrrides(): array
	{
		return [
			'collectUserData',
			'collectUDFData',
			'collectMemberData',
			'collectLpPeriod',
			'collectObjectData',
		];
	}

	/**
	 * This is never called at the base implementation
	 *
	 * @inheritDoc
	 */
	public function getOverrides(): array
	{ return []; }

	/**
	 * @inheritDoc
	 */
	public function collectUserData(EventModel $event): array
	{
		$data = [];
		if ($event->getUsrId() !== -1) {
			/** @var \ilObjUser $user */
			$user = new \ilObjUser($event->getUsrId());
			$data['user_id'] = $user->getId();
			$data['username'] = $user->getLogin();
			$data['firstname'] = $user->getFirstname();
			$data['lastname'] = $user->getLastname();
			$data['title'] = $user->getTitle();
			$data['gender'] = $user->getGender();
			$data['email'] = $user->getEmail();
			$data['institution'] = $user->getInstitution();
			$data['street'] = $user->getStreet();
			$data['city'] = $user->getCity();
			$data['country'] = $user->getCountry();
			$data['phone_office'] = $user->getPhoneOffice();
			$data['hobby'] = $user->getHobby();
			$data['department'] = $user->getDepartment();
			$data['phone_home'] = $user->getPhoneHome();
			$data['phone_mobile'] = $user->getPhoneMobile();
			$data['phone_fax'] = $user->getFax();
			$data['referral_comment'] = $user->getComment();
			$data['matriculation'] = $user->getMatriculation();
			$data['active'] = $user->getActive();
			$data['approval_date'] = $user->getApproveDate();
			$data['agree_date'] = $user->getAgreeDate();
			$data['auth_mode'] = $user->getAuthMode();
			$data['ext_account'] = $user->getExternalAccount();
			$data['birthday'] = $user->getBirthday();
			$data['import_id'] = $user->getImportId();
		}
		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function collectUDFData(EventModel $event): array
	{
		$data = [];
		if ($event->getUsrId() !== -1) {
			include_once('./Services/User/classes/class.ilUserDefinedFields.php');
			/** @var \ilUserDefinedFields $udfObj */
			$udfObj = \ilUserDefinedFields::_getInstance();
			$udef = $udfObj->getVisibleDefinitions();

			include_once("./Services/User/classes/class.ilUserDefinedData.php");
			/** @var \ilUserDefinedData $uddObj */
			$uddObj = new \ilUserDefinedData($event->getUsrId());
			$udata = $uddObj->getAll();

			foreach ($udef as $field_id => $definition) {
				$data[$field_id] = (isset($udata[$field_id]) ? $udata[$field_id] : NULL);
			}
		}
		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function collectMemberData(EventModel $event): array
	{
		$data = [];
		if (($event->getObjId() !== -1 || $event->getRefId() !== -1) && $event->getUsrId() !== -1) {
			/** @var \ilObject $ilObj */
			if($event->getRefId() !== -1){
				$ilObj = \ilObjectFactory::getInstanceByRefId($event->getRefId());
			} else {
				$ilObj = \ilObjectFactory::getInstanceByObjId($event->getObjId());
			}

			// check if object is type course
			if ($ilObj->getType() === 'crs') {
				$crs_title = $ilObj->getTitle();
				$crs_id = $ilObj->getId();
				$crs_ref_id = $ilObj->getRefId();

			} else {

				// check if any parent object is of type course
				$parent = $this->findParentCourse($ilObj->getRefId());
				if ($parent === 0) {
					$crs_title = NULL;
					$crs_id = NULL;
					$crs_ref_id = NULL;
				} else {
					/** @var \ilObject $parentObj */
					$parentObj = new \ilObject($parent, true);
					$crs_title = $parentObj->getTitle();
					$crs_id = $parentObj->getId();
					$crs_ref_id = $parentObj->getRefId();
				}

			}

			$data['role'] = ($event->getRoleId() !== -1 ? $event->getRoleId() : NULL);
			$data['course_title'] = $crs_title;
			$data['course_id'] = $crs_id;
			$data['course_ref_id'] = $crs_ref_id;
		}
		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function collectLpPeriod(EventModel $event): array
	{
		$data = [];
		if ($event->getObjId() !== -1 || $event->getRefId() !== -1) {
			/** @var \ilObject $ilObj */
			if($event->getRefId() !== -1){
				$ilObj = \ilObjectFactory::getInstanceByRefId($event->getRefId());
			} else {
				$ilObj = \ilObjectFactory::getInstanceByObjId($event->getObjId());
			}

			$course = false;
			// check if object is type course
			if ($ilObj->getType() === 'crs') {
				$course = $ilObj;
			} elseif ($ilObj->getRefId() > 0) {
				$parent = $this->findParentCourse($ilObj->getRefId());
				if ($parent !== 0) {
					$course = \ilObjectFactory::getInstanceByRefId($parent);
				}
			}

			if ($course !== false) {
				/** @var \ilObjCourse $course */
				$data['course_start'] = $course->getCourseStart();
				$data['course_end'] = $course->getCourseEnd();
			}
		}
		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function collectObjectData(EventModel $event): array
	{
		$data = [];
		if ($event->getObjId() !== -1 || $event->getRefId() !== -1) {
			/** @var \ilObject $ilObj */
			if($event->getRefId() !== -1){
				$ilObj = \ilObjectFactory::getInstanceByRefId($event->getRefId());
			} else {
				$ilObj = \ilObjectFactory::getInstanceByObjId($event->getObjId());
			}
			global $DIC;
			// check if object is type course
			if ($ilObj->getType() === 'crs') {
				/** @var \ilObject $course */
				$course_id = $ilObj->getRefId();
			} else {
				$parent = $this->findParentCourse($ilObj->getRefId());
				if ($parent !== 0) {
					$course_id = $parent;
				}
			}


			$crs_title = NULL;
			$crs_id = NULL;
			$crs_ref_id = NULL;
			if ($course_id !== false) {
				/** @var \ilObjCourse $course */
				$course = new \ilObjCourse($course_id, true);
				$crs_title = $course->getTitle();
				$crs_id = $course->getId();
				$crs_ref_id = $course->getRefId();
			}

			$link = '';
			if ($ilObj->getRefId() !== null && $ilObj->getType() !== null) {
				$link = \ilLink::_getStaticLink($ilObj->getRefId(), $ilObj->getType());
			}

			$data['id'] = $ilObj->getId();
			$data['title'] = $ilObj->getTitle();
			$data['ref_id'] = $ilObj->getRefId();
			$data['link'] = $link;
			$data['type'] = $ilObj->getType();
			$data['course_title'] = $crs_title;
			$data['course_id'] = $crs_id;
			$data['course_ref_id'] = $crs_ref_id;

		}
		return $data;
	}

	/**
	 * Find parent course object
	 *
	 * @param int $ref_id
	 * @return int
	 *         Returns 0 (zero) if no parent course could be found
	 *         otherwise the ref_id of the course object
	 */
	protected function findParentCourse($ref_id)
	{
		if (isset($ref_id)) {
			global $DIC;
			$tree = $DIC->repositoryTree();
			$parent = 0;
			// check if parent object is type course
			$parent_type = $tree->checkForParentType($ref_id, 'crs');

			if ($parent_type === false || $parent_type === 0) {
				// walk tree and check if parent object of any node is type course
				$paths = $tree->getPathFull($ref_id);
				foreach (array_reverse($paths) as $path) {
					$parent_type = $tree->checkForParentType($path['id'], 'crs');
					if ($parent_type !== false && $parent_type > 0) {
						$parent = $path['id'];
						break;
					}
				}
			}

			if ($parent_type === false || $parent_type === 0) {
				return 0;
			}
			return $parent;
		}
		return 0;
	}

}