<?php

class CRM_Calendar_Common_Case {

  /**
   * Type of event, for filter
   */
  const TYPE_CASES = 'case';

  /**
   * Color of events on calendar frontend
   */
  const CASE_COLOR = '#ff7770';

  /**
   * Get cases of contact(s)
   *
   * @param $contactIds
   * @param $params
   * @param $fields
   *
   * @return array
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public static function getCases($contactIds, $params, $fields) {
    $caseStatusId = CRM_Utils_Request::retrieve('caseStatusId', 'String');
    $caseTypeId = CRM_Utils_Request::retrieve('caseTypeId', 'String');

    $result = [];

    $query = "
      SELECT
        DISTINCT civicrm_activity.id AS activity_id,
        civicrm_case_activity.case_id AS id,
        (CASE
           WHEN civicrm_activity.subject IS NOT NULL THEN civicrm_activity.subject
           WHEN civicrm_activity.subject IS NULL THEN civicrm_case.subject
        END) AS title,
        civicrm_case.subject AS case_title,
        civicrm_activity.subject AS activity_title,
        activity_type_value.label AS activityType,
        priority_value.label AS priority,
        civicrm_activity.activity_date_time AS start,
        DATE_ADD(`activity_date_time`, INTERVAL COALESCE (`duration`, 15) MINUTE) AS `end`,
        civicrm_activity_contact.contact_id,
        contact.display_name,
        contact.contact_type,
        contact.image_URL
        
      FROM `civicrm_case`
      
      JOIN `civicrm_case_activity` ON civicrm_case_activity.case_id = civicrm_case.id
      JOIN `civicrm_activity` ON civicrm_activity.id = civicrm_case_activity.activity_id
      JOIN `civicrm_activity_contact` ON civicrm_activity_contact.activity_id = civicrm_activity.id
      LEFT JOIN `civicrm_option_group` AS priority_group ON priority_group.name = 'priority'
      LEFT JOIN `civicrm_option_value` AS priority_value 
        ON (priority_value.option_group_id = priority_group.id AND civicrm_activity.priority_id = priority_value.value)
      LEFT JOIN `civicrm_option_group` AS activity_type_group ON activity_type_group.name = 'activity_type'
      LEFT JOIN `civicrm_option_value` AS activity_type_value 
        ON (activity_type_value.option_group_id = activity_type_group.id AND civicrm_activity.activity_type_id = activity_type_value.value )
      LEFT JOIN civicrm_case_type AS case_type 
        ON civicrm_case.case_type_id = case_type.id 
      LEFT JOIN civicrm_contact AS contact
        ON contact.id = civicrm_activity_contact.contact_id
    ";

    $query .= ' WHERE TRUE ';

    $whereCondition = ' 
      AND civicrm_activity_contact.contact_id IN (' . implode(', ', $contactIds) . ') 
      AND civicrm_activity.is_deleted = 0 AND civicrm_case.is_deleted = 0
      AND `activity_date_time` BETWEEN %1 AND %2
      AND  civicrm_case_activity.activity_id IS NOT NULL
    ';

    if (!empty($caseTypeId)) {
      $whereCondition .= ' AND case_type.id IN (' . implode(', ', $caseTypeId) . ') ';
    }

    if ($params['hide_past_events'] == "1") {
      $query .= ' AND civicrm_activity.activity_date_time > NOW()';
    }

    if (!empty($caseStatusId)) {
      $whereCondition .= ' AND civicrm_case.status_id IN (' . implode(', ', $caseStatusId) . ') ';
    }

    $query .= $whereCondition;

    $caseCategories = CRM_Calendar_Settings::get(['case_types'])['case_types'];
    if (!empty($caseCategories)) {
      $query .= ' AND civicrm_case.case_type_id IN (' . implode(', ', $caseCategories) . ') ';
    }

    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [ $params['startDate'], 'String' ],
      2 => [ $params['endDate'], 'String' ]
    ]);

    while ($dao->fetch()) {
      $eventData = [
        'url' => htmlspecialchars_decode(CRM_Utils_System::url('civicrm/case/activity/view', 'cid=' . $dao->contact_id . '&aid=' . $dao->activity_id)),
        'id' => $dao->activity_id,
        'case_title' => $dao->case_title,
        'assignContact' => CRM_Calendar_Common_Activity::getAssignContactNameByActivityId($dao->activity_id),
        'constraint' => TRUE,
        'color' => self::CASE_COLOR,
        'type' => self::TYPE_CASES,
        'contact_id' => $dao->contact_id,
        'display_name' => $dao->display_name,
        'image_url' => (!empty($dao->image_URL)) ? $dao->image_URL : CRM_Calendar_Utils_Extension::getDefaultImageUrl($dao->contact_type),
      ];

      foreach ($fields as $k) {
        if (isset($dao->$k)) {
          $eventData[$k] = $dao->$k;
        }
      }

      $result[] = $eventData;
    }

    $dao->free();

    return $result;
  }

}