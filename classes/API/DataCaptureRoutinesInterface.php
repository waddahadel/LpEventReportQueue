<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\API;

use QU\LERQ\Model\EventModel;

interface DataCaptureRoutinesInterface
{
	/**
	 * Array with TRUE / FALSE values to define which functions should be overwritten
	 *   Overrides will work only if this function return an array with the functions' key and value TRUE
	 *
	 * @example
	 * return [
	 *   'collectUserData'   => false,
	 *   'collectUDFData'    => false,
	 *   'collectMemberData' => false,
	 *   'collectLpPeriod'   => true,
	 *   'collectObjectData' => false,
	 * ];
	 *
	 * @return array
	 */
	public function getOverrides(): array;

	/**
	 * Array with collectable user data
	 *
	 * expected keys:
	 *   user_id
	 *   username
	 *   firstname
	 *   lastname
	 *   title
	 *   gender
	 *   email
	 *   institution
	 *   street
	 *   city
	 *   country
	 *   phone_office
	 *   hobby
	 *   phone_home
	 *   phone_mobile
	 *   phone_fax
	 *   referral_comment
	 *   matriculation
	 *   active
	 *   approval_date
	 *   agree_date
	 *   auth_mode
	 *   ext_account
	 *   birthday
	 *   import_id
	 *
	 * @param EventModel $event
	 * @return array
	 */
	public function collectUserData(EventModel $event): array;

	/**
	 * Array with udf data
	 *
	 * recommendation:
	 *   To get the same output, everytime the function is called,
	 *   you should return all field ids with null values, if the
	 *   user has no data for this field.
	 *
	 * @param EventModel $event
	 * @return array
	 */
	public function collectUDFData(EventModel $event): array;

	/**
	 * Array with member data
	 *
	 * expected keys:
	 *   role,
	 *   course_title
	 *   course_id
	 *   course_ref_id
	 *
	 * @param EventModel $event
	 * @return array
	 */
	public function collectMemberData(EventModel $event): array;

	/**
	 * Array of learning progress period
	 *   e.g. course start and course end
	 *
	 * expected keys:
	 *   course_start
	 *   course_end
	 *
	 * @param EventModel $event
	 * @return array
	 */
	public function collectLpPeriod(EventModel $event): array;

	/**
	 * Array of object data
	 *
	 * expected keys:
	 *   id
	 *   title
	 *   ref_id
	 *   link
	 *   type
	 *   course_title
	 *   course_id
	 *   course_ref_id
	 *
	 * @param EventModel $event
	 * @return array
	 */
	public function collectObjectData(EventModel $event): array;
}