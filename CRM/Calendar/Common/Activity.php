<?php

class CRM_Calendar_Common_Activity {

  /**
   * Type of event, for filter
   */
  const TYPE_ACTIVITIES = 'activity';

  /**
   * Color of events on calendar frontend
   */
  const ACTIVITY_COLOR = '#F7CF5D';

  /**
   * Activity Contact: record_type_id -Nature of this contact's role in the
   * activity: 1 assignee, 2 creator, 3 focus or target.
   */
  const ACTIVITY_ROLE_ASSIGNEE_ID = 1;

  const ACTIVITY_ROLE_CREATOR_ID = 2;

  const ACTIVITY_ROLE_FOCUS_OR_TARGET_ID = 3;

  /**
   * Get all activities for contact
   *
   * @param $contactIds
   * @param $params
   * @param $fields
   *
   * @return array
   * @throws \CRM_Core_Exception
   */
  public static function getActivities($contactIds, $params, $fields) {

    $activityRoleId = CRM_Utils_Request::retrieve('activityRoleId', 'String');
    $activityStatusId = CRM_Utils_Request::retrieve('activityStatusId', 'String');
    $activityTypeId = CRM_Utils_Request::retrieve('activityTypeId', 'String');
    $imagePath = CRM_Calendar_Common_Handler::getImagePath();

    $result = [];

    $query = '
      SELECT
        DISTINCT civicrm_activity.id AS id,
        civicrm_activity.`subject` AS title,
        civicrm_activity.`activity_date_time` AS start,
        activity_type_value.label AS activityType,
        priority_value.label AS priority,
        DATE_ADD(`activity_date_time`, INTERVAL COALESCE (`duration`, 15) MINUTE) AS `end`,
        civicrm_activity_contact.contact_id,
        contact.display_name,
        contact.contact_type,
        contact.image_URL
        
      FROM `civicrm_activity`
      
      JOIN `civicrm_activity_contact` ON civicrm_activity_contact.activity_id = civicrm_activity.id
      LEFT JOIN `civicrm_case_activity` ON civicrm_case_activity.activity_id = civicrm_activity.id
      LEFT JOIN `civicrm_option_group` AS priority_group ON priority_group.name = "priority"
      LEFT JOIN `civicrm_option_value` AS priority_value 
        ON (priority_value.option_group_id = priority_group.id AND civicrm_activity.priority_id = priority_value.value)
      
      LEFT JOIN `civicrm_option_group` AS activity_type_group ON activity_type_group.name = "activity_type"
      LEFT JOIN `civicrm_option_value` AS activity_type_value 
        ON (activity_type_value.option_group_id = activity_type_group.id AND civicrm_activity.activity_type_id = activity_type_value.value )
      
      LEFT JOIN `civicrm_option_group` AS activity_visibility_group ON activity_visibility_group.name = "activity_visibility"
		  LEFT JOIN `civicrm_option_value` AS activity_visibility_value 
      	ON (activity_visibility_value.option_group_id = activity_visibility_group.id AND activity_visibility_value.value = activity_type_value.visibility_id)
      
      LEFT JOIN civicrm_contact AS contact
        ON contact.id = civicrm_activity_contact.contact_id
    ';

    if (!empty($activityRoleId) && $activityRoleId == ACTIVITY_ROLE_ASSIGNEE_ID) {
      $query .= ' 
        LEFT JOIN `civicrm_activity_contact` AS assignee_role
        ON 
        (
          civicrm_activity.id = assignee_role.activity_id 
          AND assignee_role.contact_id IN (' . implode(', ', $contactIds) . ')
          AND assignee_role.record_type_id = ' . ACTIVITY_ROLE_ASSIGNEE_ID . '
        )
       ';
    }

    if (!empty($activityRoleId) && $activityRoleId == ACTIVITY_ROLE_CREATOR_ID) {
      $query .= '
        LEFT JOIN `civicrm_activity_contact` AS creator_role
        ON 
        (
          civicrm_activity.id = creator_role.activity_id 
          AND creator_role.contact_id IN (' . implode(', ', $contactIds) . ')
          AND creator_role.record_type_id = ' . ACTIVITY_ROLE_CREATOR_ID . '
        )
       ';
    }

    if (!empty($activityRoleId) && $activityRoleId == ACTIVITY_ROLE_FOCUS_OR_TARGET_ID) {
      $query .= ' 
        LEFT JOIN `civicrm_activity_contact` AS focus_or_target_role
        ON 
        (
          civicrm_activity.id = focus_or_target_role.activity_id 
          AND focus_or_target_role.contact_id IN (' . implode(', ', $contactIds) . ')
          AND focus_or_target_role.record_type_id = ' . ACTIVITY_ROLE_FOCUS_OR_TARGET_ID . '
        )
       ';
    }

    $query .= ' WHERE TRUE ';

    $whereCondition = ' 
      AND civicrm_activity_contact.contact_id IN (' . implode(', ', $contactIds) . ')
      AND civicrm_activity.is_deleted = 0
      AND civicrm_activity.activity_date_time 
          BETWEEN "' . $params['startDate'] . '" 
          AND "' . $params['endDate'] . '"
      AND  civicrm_case_activity.activity_id IS NULL
    ';

    if (!empty($activityRoleId) && $activityRoleId == ACTIVITY_ROLE_ASSIGNEE_ID) {
      $whereCondition .= ' AND assignee_role.contact_id IS NOT NULL';
    }

    if (!empty($activityRoleId) && $activityRoleId == ACTIVITY_ROLE_CREATOR_ID) {
      $whereCondition .= ' AND creator_role.contact_id IS NOT NULL';
    }

    if (!empty($activityRoleId) && $activityRoleId == ACTIVITY_ROLE_FOCUS_OR_TARGET_ID) {
      $whereCondition .= ' AND focus_or_target_role.contact_id IS NOT NULL';
    }

    if (!empty($activityTypeId)) {
      $whereCondition .= ' AND civicrm_activity.activity_type_id IN (' . implode(', ', $activityTypeId) . ') ';
    }

    if (!empty($activityStatusId)) {
      $whereCondition .= ' AND civicrm_activity.status_id IN (' . implode(', ', $activityStatusId) . ') ';
    }

    $whereCondition .= ' AND (activity_type_value.visibility_id IS NULL OR activity_visibility_value.name = "visibility")';

    $query .= $whereCondition;
    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      $eventData = [];

      foreach ($fields as $k) {
        if (isset($dao->$k)) {
          $eventData[$k] = $dao->$k;
        }
      }

      $eventData['url'] = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/activity', 'action=view&reset=1&cid=' . $dao->contact_id . '&id=' . $dao->id));
      $eventData['id'] = $dao->id;
      $eventData['assignContact'] = self::getAssignContactNameByActivityId($eventData['id']);
      $eventData['constraint'] = TRUE;
      $eventData['type'] = self::TYPE_ACTIVITIES;
      $eventData['contact_id'] = $dao->contact_id;
      $eventData['display_name'] = $dao->display_name;
      $eventData['color'] = self::ACTIVITY_COLOR;

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

  /**
   * Get activity types
   *
   * @return array
   */
  public static function getTypes() {
    $activityTypes = [];

    $query = CRM_Utils_SQL_Select::from(CRM_Core_DAO_OptionValue::getTableName() . ' AS activity_type_value')
      ->join('activity_type_group', 'LEFT JOIN ' . CRM_Core_DAO_OptionGroup::getTableName() . ' activity_type_group ON activity_type_group.id = activity_type_value.option_group_id')
      ->where('activity_type_group.name = "activity_type" ')
      ->where('activity_type_value.is_active = 1 ');

    $dao = CRM_Core_DAO::executeQuery($query->toSQL());

    while ($dao->fetch()) {
      $activityTypes[$dao->value] = $dao->label;
    }

    return $activityTypes;
  }

  /**
   * Gets activity statuses
   *
   * @return array
   */
  public static function getStatus() {
    return CRM_Core_PseudoConstant::get('CRM_Activity_DAO_Activity', 'status_id');
  }

  /**
   * Get assign contact name by activity id
   *
   * @param $activityId
   *
   * @return string
   */
  public static function getAssignContactNameByActivityId($activityId) {
    $assignContactNames = '';

    $query = CRM_Utils_SQL_Select::from('civicrm_activity_contact')
      ->select('DISTINCT civicrm_contact.display_name')
      ->join('civicrm_contact', 'LEFT JOIN civicrm_contact ON civicrm_activity_contact.contact_id = civicrm_contact.id')
      ->where("civicrm_activity_contact.activity_id = '" . $activityId . "'")
      ->where("civicrm_activity_contact.record_type_id = '" . ACTIVITY_ROLE_ASSIGNEE_ID . "'")
      ->toSQL();

    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      $assignContactNames .= '<p>' . $dao->display_name . ';</p>';
    }

    return $assignContactNames;
  }

  /**
   * Get activities roles
   *
   * @return array
   */
  public static function getRoles() {
    $activityRoles = CRM_Core_OptionGroup::values('activity_contacts', FALSE, FALSE, FALSE, NULL, 'name');

    foreach ($activityRoles as &$val) {
      $val = ts($val);
    }

    return $activityRoles;
  }

}
