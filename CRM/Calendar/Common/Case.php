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
   */
  public static function getCases($contactIds, $params, $fields) {
    $caseStatusId = CRM_Utils_Request::retrieve('caseStatusId', 'String');
    $caseTypeId = CRM_Utils_Request::retrieve('caseTypeId', 'String');
    $imagePath = CRM_Calendar_Common_Handler::getImagePath();

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
      LEFT JOIN " . CRM_Case_DAO_CaseType::getTableName() . " AS case_type 
        ON civicrm_case.case_type_id = case_type.id 
      LEFT JOIN civicrm_contact AS contact
        ON contact.id = civicrm_activity_contact.contact_id
    ";

    $query .= ' WHERE TRUE ';

    $whereCondition = ' 
      AND civicrm_activity_contact.contact_id IN (' . implode(', ', $contactIds) . ') 
      AND civicrm_activity.is_deleted = 0 AND civicrm_case.is_deleted = 0
      AND `activity_date_time` BETWEEN "' . $params['startDate'] . '" AND "' . $params['endDate'] . '"
      AND  civicrm_case_activity.activity_id IS NOT NULL
    ';

    if (!empty($caseTypeId)) {
      $whereCondition .= ' AND case_type.id IN (' . implode(', ', $caseTypeId) . ') ';
    }

    if (!empty($caseStatusId)) {
      $whereCondition .= ' AND civicrm_case.status_id IN (' . implode(', ', $caseStatusId) . ') ';
    }

    $query .= $whereCondition;

    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      $eventData = [];

      foreach ($fields as $k) {
        if (isset($dao->$k)) {
          $eventData[$k] = $dao->$k;
        }
      }

      $eventData['url'] = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/case/activity/view', 'cid=' . $dao->contact_id . '&aid=' . $dao->activity_id));
      $eventData['id'] = $dao->activity_id;
      $eventData['case_title'] = $dao->case_title;
      $eventData['case_id'] = $dao->case_id;
      $eventData['assignContact'] = CRM_Calendar_Common_Activity::getAssignContactNameByActivityId($eventData['id']);
      $eventData['constraint'] = TRUE;
      $eventData['color'] = self::CASE_COLOR;
      $eventData['type'] = self::TYPE_CASES;
      $eventData['contact_id'] = $dao->contact_id;
      $eventData['display_name'] = $dao->display_name;

      if (!empty($dao->image_URL)) {
        $eventData['image_url'] = $dao->image_URL;
      }
      else {
        if ($dao->contact_type == 'Individual') {
          $eventData['image_url'] = $imagePath . 'Person.svg';
        }
        elseif ($dao->contact_type == 'Organization') {
          $eventData['image_url'] = $imagePath . 'Organization.svg';
        }
        else {
          $eventData['image_url'] = $imagePath . 'Person.svg';
        }
      }

      $result[] = $eventData;
    }

    $dao->free();

    return $result;
  }

}