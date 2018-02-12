<?php

class CRM_Calendar_Page_AJAX
{

  public static function getContactEventCalendar()
  {
    if(!empty($_GET['cid'])){ 
        $contact = CRM_Utils_Type::escape($_GET['cid'], 'Positive');
    }
    
    if (!$contact) {
      global $user;

      if (empty($user->uid)) {
        return FALSE;
      }

      $uid = $user->uid;
      $contact = CRM_Core_BAO_UFMatch::getContactId($uid);
    }
    
    $hidePastEvents = CRM_Calendar_Settings::getValue('hidepastevents');

    $events = array();
    $eventCalendarParams = array('title' => 'title', 'start' => 'start', 'url' => 'url', 'end' => 'end');

    /** Generate SQL query to get Contact's Events **/
    $query = '
      SELECT DISTINCT
        civicrm_event.id,
        civicrm_event.title,
        CONVERT_TZ(civicrm_event.start_date , @@session.time_zone, "+00:00") AS start,
        CONVERT_TZ(civicrm_event.end_date , @@session.time_zone, "+00:00") AS end
      FROM civicrm_event
      LEFT JOIN civicrm_participant ON civicrm_participant.event_id = civicrm_event.id
      WHERE civicrm_event.is_active = 1 
        AND civicrm_event.is_template = 0
        AND ( civicrm_event.created_id = ' . $contact . ' OR civicrm_participant.contact_id = ' . $contact . ')
        AND (
          civicrm_event.start_date BETWEEN "' . gmdate("Y-m-d H:i:s", $_REQUEST["start"]) . '" AND "' . gmdate("Y-m-d H:i:s", $_REQUEST["end"]) . '" 
          OR civicrm_event.end_date BETWEEN "' . gmdate("Y-m-d H:i:s", $_REQUEST["start"]) . '" AND "' . gmdate("Y-m-d H:i:s", $_REQUEST["end"]) . '" 
          OR "' . gmdate("Y-m-d H:i:s", $_REQUEST["start"]) . '" BETWEEN civicrm_event.start_date AND civicrm_event.end_date
        )
    ';

    /** Hide Past Events when hidePastEvents setting is enabled **/
    if ($hidePastEvents == "1") {
       $query .= ' AND civicrm_event.start_date > NOW()';
    }

    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      if ($dao->title) {
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/event/info', 'reset=1&id=' . $dao->id ));
      }

      $eventData = array();

      foreach ($eventCalendarParams as $k) {
        $eventData[$k] = $dao->$k;
      }

      $eventData['constraint'] = true;
      $eventData['color'] = '#35D0AE';
      $events['events'][] = $eventData;
    }

    $dao->free();

    /** Generate SQL query to get Contact's Cases **/
    $query = '
      SELECT 
        civicrm_case.id AS id,
        civicrm_case_activity.activity_id AS activity_id,
        CONCAT(COALESCE(civicrm_activity.subject,civicrm_case.subject,"")," (",civicrm_option_value.name,")") AS title,
        civicrm_activity.activity_date_time AS start,
        DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE) AS end
      
      FROM civicrm_case
      
      JOIN civicrm_case_contact ON civicrm_case_contact.case_id = civicrm_case.id
      JOIN civicrm_case_activity ON civicrm_case_activity.case_id = civicrm_case.id
      JOIN civicrm_activity ON civicrm_activity.id = civicrm_case_activity.activity_id
      JOIN civicrm_option_value ON civicrm_activity.activity_type_id = civicrm_option_value.value
      JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id AND civicrm_option_group.name = "activity_type" AND civicrm_option_value.component_id IS NOT NULL
      
      WHERE civicrm_case_contact.contact_id = ' . $contact . '
      AND civicrm_case.is_deleted=0 AND civicrm_activity.is_deleted=0
      AND ( (civicrm_activity.activity_date_time >= "' . gmdate("Y-m-d H:i:s", $_REQUEST["start"]) . '"
      AND COALESCE (DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE),civicrm_activity.activity_date_time) <= "' . gmdate("Y-m-d H:i:s", $_REQUEST["end"]) . '" )
      OR ("' . date("Y-m-d H:i:s", $_REQUEST["start"]) . '" BETWEEN civicrm_activity.activity_date_time AND COALESCE (DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE),civicrm_activity.activity_date_time)))
    ';

    /** Hide Past Cases when hidePastEvents setting is enabled **/
    if ($hidePastEvents == "1") {
       $query .= ' AND civicrm_activity.activity_date_time > NOW()';
    }

    $dao = CRM_Core_DAO::executeQuery($query);

    $i = 1;

    while ($dao->fetch()) {
      if ($dao->title) {
        $startDate = new DateTime($dao->start);
        $startDate->modify('+' . $i . ' second');

        $dao->start = $startDate->format('Y-m-d H:i:s');
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/case/activity/view', 'cid=' . $contact . '&aid=' . $dao->activity_id));
      }

      $eventData = array();

      foreach ($eventCalendarParams as $k) {
        $eventData[$k] = $dao->$k;
      }

      $eventData['constraint'] = true;
      $eventData['color'] = '#ff0000';
      $eventData['type'] = 'case';
      $events['case'][] = $eventData;

      $i++;
    }

    $dao->free();

    /** Generate SQL query to get Contact's Activities **/
    $query = '
      SELECT DISTINCT
        civicrm_activity.id AS id,
        civicrm_activity.subject AS title,
        civicrm_activity.activity_date_time AS start,
        DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE) AS end
      
      FROM civicrm_activity
      JOIN civicrm_activity_contact ON civicrm_activity_contact.activity_id = civicrm_activity.id
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      
      WHERE civicrm_activity_contact.contact_id = "' . $contact . '" AND (civicrm_activity.activity_date_time > "' . date("Y-m-d H:i:s", $_REQUEST["start"]) . '" AND civicrm_activity.activity_date_time < "' . date("Y-m-d H:i:s", $_REQUEST["end"]) . '") AND civicrm_case_activity.activity_id IS NULL
        AND civicrm_activity.is_deleted=0      
        AND activity_type_id IN (
          SELECT civicrm_option_value.value FROM civicrm_option_value
          JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
          WHERE civicrm_option_group.name = "activity_type" 
            AND civicrm_option_value.component_id IS NULL
        )
    ';

    /** Hide Past Activities when hidePastEvents setting is enabled **/
    if ($hidePastEvents == "1") {
       $query .= ' AND civicrm_activity.activity_date_time > NOW()';
    }

    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      if ($dao->title) {
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/activity', 'action=view&reset=1&cid=' . $contact . '&id=' . $dao->id));
      }

      $eventData = array();

      foreach ($eventCalendarParams as $k) {
        $eventData[$k] = $dao->$k;
      }

      $eventData['constraint'] = true;
      $eventData['color'] = '#F7CF5D';
      $events['activity'][] = $eventData;
    }

    $dao->free();

    CRM_Utils_JSON::output($events);
  }
}