<?php

namespace QU\LERQ\Helper;

use QU\LERQ\Events\FakeEvent;

class InitialQueueCollector
{
    public function collectWayTooMuchDataFromDatabase()
    {
        global $DIC;

        $query = 'SELECT `rua`.`usr_id` ,`oref`.`ref_id` ,`oref`.`obj_id` ,`rua`.`rol_id` ,`od`.`type` ,`od`.`title`
    ,`ud`.`login` ,`ud`.`firstname` ,`ud`.`lastname` ,`ud`.`title` ,`ud`.`gender` ,`ud`.`email` ,`ud`.`institution` 
    ,`ud`.`street` ,`ud`.`city` ,`ud`.`country`	,`ud`.`phone_office` ,`ud`.`hobby` ,`ud`.`department` ,`ud`.`phone_home` 
    ,`ud`.`phone_mobile` ,`ud`.`fax` ,`ud`.`referral_comment` ,`ud`.`matriculation` ,`ud`.`active` ,`ud`.`approve_date` 
    ,`ud`.`agree_date` ,`ud`.`auth_mode` ,`ud`.`ext_account` ,`ud`.`birthday` ,`uod`.`import_id`
    ,CASE 
        WHEN `od`.`type` = "crs" THEN `od`.`title`
        ELSE NULL
    END AS "course_title"
    ,CASE
        WHEN `od`.`type` = "crs" THEN `od`.`obj_id`
        ELSE NULL
    END AS "course_id"
    ,CASE
        WHEN `od`.`type` = "crs" THEN `oref`.`ref_id`
        ELSE NULL
    END AS "course_ref_id"
    ,`ulm`.`status`
FROM object_reference oref 
LEFT JOIN `rbac_fa` `rfa` 
    ON `rfa`.`parent` = `oref`.`ref_id` 
LEFT JOIN `rbac_ua` `rua` 
    ON `rua`.`rol_id` = `rfa`.`rol_id` 
LEFT JOIN `object_data` `od` 
    ON `od`.`obj_id` = `oref`.`obj_id` 
JOIN `usr_data` `ud` 
    ON `rua`.`usr_id` = `ud`.`usr_id`
JOIN `object_data` `uod` 
    ON `ud`.`usr_id` = `uod`.`obj_id`
JOIN `ut_lp_marks` `ulm` 
    ON `ulm`.`obj_id` = `oref`.`obj_id` 
    AND `ulm`.`usr_id` = `ud`.`usr_id`
WHERE `rfa`.`assign` = "y" 
    AND `rua`.`rol_id` IS NOT NULL 
    AND `od`.`type` NOT IN ("rolf", "role")
    AND `oref`.`ref_id` >= 0';

        $result = $DIC->database()->query($query);
        return $DIC->database()->fetchAll($result);
    }

    /**
     * Collect Base Data
     *
     * @return array
     */
    public function collectBaseDataFromDB(): array
    {
        global $DIC;

        $result = $DIC->database()->query($this->getBaseDataQuery([
            'rua.usr_id', 'oref.ref_id', 'oref.obj_id', 'rua.rol_id', 'od.type', 'ulm.status', 'ulm.status_changed'
            ,'od.title' ,'cs.crs_start' ,'cs.crs_end'
        ]));
        return $DIC->database()->fetchAll($result);
    }

    /**
     * Collect User Data
     *
     * @return array
     */
    public function collectUserDataFromDB(): array
    {
        global $DIC;

        $query = 'SELECT `ud`.`usr_id`
	,`ud`.`login` ,`ud`.`firstname` ,`ud`.`lastname` ,`ud`.`title` ,`ud`.`gender` ,`ud`.`email` ,`ud`.`institution` 
	,`ud`.`street` ,`ud`.`city` ,`ud`.`country`	,`ud`.`phone_office` ,`ud`.`hobby` ,`ud`.`department` ,`ud`.`phone_home` 
	,`ud`.`phone_mobile` ,`ud`.`fax` ,`ud`.`referral_comment` ,`ud`.`matriculation` ,`ud`.`active` ,`ud`.`approve_date` 
	,`ud`.`agree_date` ,`ud`.`auth_mode` ,`ud`.`ext_account` ,`ud`.`birthday` ,`uod`.`import_id`
FROM `usr_data` `ud`
JOIN `object_data` `uod` ON `uod`.`obj_id` = `ud`.`usr_id`
WHERE `ud`.`usr_id` IN (' .
            $this->getBaseDataQuery(['rua.usr_id'], 0, [], true) .
')';
        $result = $DIC->database()->query($query);

        include_once('./Services/User/classes/class.ilUserDefinedFields.php');
        /** @var \ilUserDefinedFields $udfObj */
        $udfObj = \ilUserDefinedFields::_getInstance();
        $udef = $udfObj->getVisibleDefinitions();

        $data = [];
        while($row = $DIC->database()->fetchAssoc($result)) {
            $data[$row['usr_id']] = $row;

            include_once("./Services/User/classes/class.ilUserDefinedData.php");
            /** @var \ilUserDefinedData $uddObj */
            $uddObj = new \ilUserDefinedData($row['usr_id']);
            $udata = $uddObj->getAll();

            foreach ($udef as $field_id => $definition) {
                $data[$row['usr_id']]['udfdata'][$field_id] = (isset($udata[$field_id]) ? $udata[$field_id] : NULL);
            }

        }

        return $data;
    }

    /**
     * Collect Object Data
     *
     * @return mixed
     */
    public function collectObjectDataFromDB(): array
    {
        global $DIC;

        $tree = $DIC->repositoryTree();
        $result = $DIC->database()->query($this->getBaseDataQuery(['oref.ref_id' ,'oref.obj_id' ,'od.type' ,'od.title'], 0, ['crs']));
        $refs = [];
        while($row = $DIC->database()->fetchAssoc($result)) {
            $parent_type = $tree->checkForParentType($row['ref_id'], 'crs');
            if ($parent_type === false || $parent_type === 0) {
                $paths = $tree->getPathFull($row['ref_id']);
                if (is_array($paths) && count($paths) > 0) {
                    foreach (array_reverse($paths) as $path) {
                        $parent_type = $tree->checkForParentType($path['id'], 'crs');
                        if ($parent_type !== false && $parent_type > 0) {
                            $refs[$row['ref_id']] = $path['id'];
                            break;
                        }
                    }
                }
            } else {
                $refs[] = $parent_type;
            }
        }

        $query2 = 'SELECT `oref`.`ref_id` ,`oref`.`obj_id` ,`od`.`type` ,`od`.`title` ,`cs`.`crs_start` ,`cs`.`crs_end`
FROM `object_reference` `oref`
LEFT JOIN `object_data` `od` 
    ON `od`.`obj_id` = `oref`.`obj_id` 
RIGHT JOIN `crs_settings` `cs`
    ON `cs`.`obj_id` = `od`.`obj_id`
WHERE `od`.`type` = "crs"';
//WHERE `oref`.`ref_id` IN (' . implode(',', $refs) . ')';

        $result2 = $DIC->database()->query($query2);
        $data = [
            'courses' => [],
            'map' => $refs,
        ];
        while($row2 = $DIC->database()->fetchAssoc($result2)) {
            $data['courses'][$row2['ref_id']] = $row2;
        }

        return $data;
    }

    public function collectDataAndTriggerEvents()
    {
        $response = [
            'addParticipant' => [],
            'updateStatus' => [],
        ];

        $base_data = $this->collectBaseDataFromDB();
        $object_data = $this->collectObjectDataFromDB();
        $user_data = $this->collectUserDataFromDB();

        $eventDataAggregator = EventDataAggregationHelper::singleton();

        $event = new FakeEvent();
        
        foreach ($base_data as $bd) {
            if ($bd['type'] == 'crs') {
                $od = $bd;
            } else {
                $od = $object_data['courses'][$object_data['map'][$bd['ref_id']]];
            }
            $ud = $user_data[$bd['usr_id']];
            $aggregated = [
                'progress' => $eventDataAggregator->getLpStatusRepresentation($bd['status']),
                'progress_changed' => $bd['status_changed'], // todo: new parameter
                'assignment' => $eventDataAggregator->getRoleTitleByRoleId($bd['rol_id']),
                'lpperiod' => [
                    'course_start' => new \ilDate($od['crs_start'], IL_CAL_UNIX),
                    'course_end' => new \ilDate($od['crs_end'], IL_CAL_UNIX),
                ],
                'userdata' => [
                    'usr_id' => $ud['usr_id'],
                    'username' => $ud['login'],
                    'firstname' => $ud['firstname'],
                    'lastname' => $ud['lastname'],
                    'title' => $ud['title'],
                    'gender' => $ud['gender'],
                    'email' => $ud['email'],
                    'institution' => $ud['institution'],
                    'street' => $ud['street'],
                    'city' => $ud['city'],
                    'country' => $ud['country'],
                    'phone_office' => $ud['phone_office'],
                    'hobby' => $ud['hobby'],
                    'department' => $ud['department'],
                    'phone_home' => $ud['phone_home'],
                    'phone_mobile' => $ud['phone_mobile'],
                    'fax' => $ud['fax'],
                    'referral_comment' => $ud['referral_comment'],
                    'matriculation' => $ud['matricualtion'],
                    'active' => $ud['active'],
                    'approval_date' => $ud['approve_date'],
                    'agree_date' => $ud['agree_date'],
                    'auth_mode' => $ud['auth_mode'],
                    'ext_account' => $ud['ext_account'],
                    'birthday' => $ud['birthday'],
                    'import_id' => $ud['import_id'],
                ],
                'udfdata' => $ud['udfdata'],
                'objectdata' => [
                    'title' => $bd['title'],
                    'id' => $bd['obj_id'],
                    'ref_id' => $bd['ref_id'],
                    'link' => \ilLink::_getStaticLink($bd['ref_id'], $bd['type']),
                    'type' => $bd['type'],
                    'course_title' => $od['title'],
                    'course_id' => $od['obj_id'],
                    'course_ref_id' => $od['ref_id'],
                ],
                'memberdata' => [
                    'role' => $bd['rol_id'],
                    'course_title' => $od['title'],
                    'course_id' => $od['obj_id'],
                    'course_ref_id' => $od['ref_id'],
                ],
            ];

            $response['addParticipant'][$bd['ref_id']] = $event->handle_event('addParticipant', $aggregated);
            $response['updateStatus'][$bd['ref_id']] = $event->handle_event('updateStatus', $aggregated);
        }

        return $response;
    }


    private function getBaseDataQuery(array $field_list, int $ref_id = 0, array $exclude_types = [], $distinct = false)
    {
        return 'SELECT ' . ($distinct ? 'DISTINCT ' : '') . implode(',', $field_list) . '
FROM `object_reference` `oref` 
LEFT JOIN `rbac_fa` `rfa` 
    ON `rfa`.`parent` = `oref`.`ref_id` 
LEFT JOIN `rbac_ua` `rua` 
    ON `rua`.`rol_id` = `rfa`.`rol_id` 
LEFT JOIN `object_data` `od` 
    ON `od`.`obj_id` = `oref`.`obj_id` 
JOIN `usr_data` `ud` 
    ON `rua`.`usr_id` = `ud`.`usr_id`
JOIN `object_data` `uod` 
    ON `ud`.`usr_id` = `uod`.`obj_id`
JOIN `ut_lp_marks` `ulm` 
    ON `ulm`.`obj_id` = `oref`.`obj_id` 
    AND `ulm`.`usr_id` = `ud`.`usr_id`
RIGHT JOIN `crs_settings` `cs`
    ON `cs`.`obj_id` = `od`.`obj_id`
WHERE `rfa`.`assign` = "y" 
    AND `rua`.`rol_id` IS NOT NULL 
    AND `od`.`type` NOT IN ("rolf", "role' . (!empty($exclude_types) ? implode('","', $exclude_types) : '') . '") 
    AND `oref`.`ref_id` >= ' . $ref_id . '
    AND `rua`.`usr_id` != 6
    AND `rua`.`usr_id` != 0';
    }

}