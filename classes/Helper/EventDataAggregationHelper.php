<?php

namespace QU\LERQ\Helper;

/**
 * Class EventDataAggregationHelper
 * @package QU\LERQ\Helper
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class EventDataAggregationHelper
{
	/** @var EventDataAggregationHelper */
	protected static $instance;

	/** @var \ilLogger  */
	protected $logger;

	/**
	 * EventDataAggregationHelper constructor.
	 */
	public function __construct()
	{
		global $DIC;
		$this->logger = $DIC->logger()->root();
	}

	/**
	 * Get singleton
	 *
	 * @return EventDataAggregationHelper
	 */
	public static function singleton()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get learning progress status representation by lp.status
	 *
	 * @param int $status
	 * @return string
	 */
	public function getLpStatusRepresentation(int $status = Null): string
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

	/**
	 * Get readable role title by role_id
	 *
	 * @param int $role_id
	 * @return string
	 */
	public function getRoleTitleByRoleId(int $role_id): string
	{
		/** @var \ilObjRole $roleObj */
		$roleObj = \ilObjectFactory::getInstanceByObjId($role_id);

		$found_num = preg_match('/(member|tutor|admin)/', $roleObj->getTitle(), $matches);
		$role_title = '';
		if ($found_num > 0) {
			switch ($matches[0]) {
				case 'member':
					$role_title = 'member';
					break;
				case 'tutor':
					$role_title = 'tutor';
					break;
				case 'admin':
					$role_title = 'administrator';
					break;
				default:
					$role_title = $roleObj->getTitle();
					break;
			}
		}

		return $role_title;
	}

	/**
	 * Get role id of parent container object assignment
	 *
	 * @param int $ref_id
	 * @param int $user_id
	 * @return int
	 */
	public function getParentContainerAssignmentRoleForObjectByRefIdAndUserId(?int $ref_id, int $user_id): int
	{
		if (!isset($ref_id)) {
			return -1;
		}
		global $DIC;
		$cont_ref_id = $this->getContainerRefIdByObjectRefIdAndTypes($ref_id);

		$this->logger->debug(sprintf('called "%s" with ref_id "%s" and user_id "%s"',
			'getParentContainerAssignmentRoleForObjectByRefIdAndUserId',
			$ref_id,
			$user_id
		));

		$select_assignments = 'SELECT rua.rol_id FROM object_reference oref ' .
			'LEFT JOIN rbac_fa rfa ON rfa.parent = oref.ref_id ' .
			'LEFT JOIN rbac_ua rua ON rua.rol_id = rfa.rol_id ' .
			'WHERE rfa.assign = "y" ' .
			'AND rua.rol_id IS NOT NULL ' .
			'AND rua.usr_id = ' . $DIC->database()->quote($user_id, 'integer') . ' ';
		$select_assignments .= 'AND oref.ref_id = ' . $DIC->database()->quote($cont_ref_id, 'integer') . ' ';


		$result = $DIC->database()->query($select_assignments);
		$assignments = $DIC->database()->fetchAll($result);

		if (!empty($assignments) && array_key_exists('rol_id', $assignments[0])) {
			$this->logger->debug(sprintf('role_id %s found', $assignments[0]['rol_id']));
			return $assignments[0]['rol_id'];
		} else {
			$this->logger->debug(sprintf('no role found'));
			return -1;
		}

	}

	/**
	 * Get parent container ref_id by matching container types
	 *
	 * @param int $ref_id
	 * @param array $types
	 * @return int
	 */
	public function getContainerRefIdByObjectRefIdAndTypes(int $ref_id, array $types = []): int
	{
		if (!isset($type) || empty($type)) {
			$types = ['crs', 'grp', 'prg'];
		}
		$this->logger->debug(sprintf('called %s with ref_id %s and types: [%s]',
			'getContainerRefIdByObjectRefIdAndTypes',
			$ref_id,
			implode(',', $types)
		));

		$cont_ref_id = $this->searchFirstParentRefIdByTypes($ref_id, $types);
		if ($cont_ref_id === false || $cont_ref_id === 0) {
			global $DIC;
			$tree = $DIC->repositoryTree();

			$paths = $tree->getPathFull($ref_id);
			$this->logger->debug(sprintf('searching in path %s', $paths));
			foreach (array_reverse($paths) as $path) {
				$this->logger->debug(sprintf('checking path item %s', $path['id']));
				$cont_ref_id = $this->searchFirstParentRefIdByTypes($path['id'], $types);

				if ($cont_ref_id !== false && $cont_ref_id > 0) {
					break;
				}
			}
		}
		// return -1 if no container was found
		if ($cont_ref_id === false || $cont_ref_id === 0) {
			$this->logger->debug(sprintf('no container ref_id found'));
			return -1;
		}

		$this->logger->debug(sprintf('container ref_id %s found', $cont_ref_id));
		return $cont_ref_id;
	}

	/**
	 * Search the first matching parent by a ref_id
	 *
	 * @param int $ref_id
	 * @param array $types
	 * @return int|bool
	 */
	private function searchFirstParentRefIdByTypes(int $ref_id, array $types)
	{
		global $DIC;
		$tree = $DIC->repositoryTree();
		$this->logger->debug(sprintf('calles %s with ref_id %s and types: [%s]',
			'searchFirstParentRefIdByTypes',
			$ref_id,
			$types
		));

		foreach ($types as $type) {
			$parent_type = $tree->checkForParentType($ref_id, $type);
			if ($parent_type !== false && $parent_type > 0) {
				break;
			}
		}

		return $parent_type;
	}

}