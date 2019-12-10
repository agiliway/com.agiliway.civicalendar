<?php

class CRM_Calendar_Common_Event {

  /**
   * Type of event, for filter
   */
  const TYPE_EVENTS = 'event';

  /**
   * Color of events on calendar frontend
   */
  const EVENT_COLOR = '#2ecaa4';

  /**
   * Get events for contact(s)
   *
   * @param $contactIds
   * @param $params
   * @param $fields
   *
   * @return array
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public static function getEvents($contactIds, $params, $fields) {
    $eventTypeId = CRM_Utils_Request::retrieve('eventTypeId', 'String');
    $eventStatusId = CRM_Utils_Request::retrieve('eventStatusId', 'String');
    $result = [];

    $query = "
      SELECT 
        civicrm_participant.role_id AS participantRoleId,
        civicrm_participant.status_id AS eventStatusId,
        civicrm_participant.id AS participant_id,
        civicrm_event.title,
        civicrm_event.id AS id,
        event_type_value.label AS eventType,
        CONVERT_TZ(civicrm_event.start_date , @@session.time_zone, '+00:00') AS start,
        CONVERT_TZ(civicrm_event.end_date , @@session.time_zone, '+00:00') AS `end`,
        civicrm_participant.contact_id AS contact_id,
        contact.display_name,
        contact.contact_type,
        contact.image_URL
      
      FROM `civicrm_event`
      
      LEFT JOIN civicrm_participant ON civicrm_event.id = civicrm_participant.event_id
      LEFT JOIN `civicrm_option_group` AS event_type_group ON event_type_group.name = 'event_type'
      LEFT JOIN `civicrm_option_value` AS event_type_value ON (event_type_value.option_group_id = event_type_group.id AND civicrm_event.event_type_id = event_type_value.value )
      LEFT JOIN civicrm_contact AS contact ON contact.id = civicrm_participant.contact_id
        
      WHERE TRUE 
    ";

    $eventCategories = CRM_Calendar_Settings::get(['event_types'])['event_types'];
    if (!empty($eventCategories)) {
      $query .= ' AND civicrm_event.event_type_id IN (' . implode(', ', $eventCategories) . ') ';
    }

    if ($params['hide_past_events'] == "1") {
      $query .= ' AND civicrm_event.start_date > NOW()';
    }

    $whereCondition = ' 
      AND civicrm_event.is_active = 1 
      AND civicrm_event.is_template = 0
      AND ( civicrm_participant.contact_id IN (' . implode(', ', $contactIds) . ') OR  civicrm_event.created_id IN(' . implode(', ', $contactIds) . '))
      AND NOT
        (
          (
            DATE(civicrm_event.start_date) BETWEEN %1 AND %2
            OR DATE(civicrm_event.end_date) >= %1
          )
          XOR
          (
            DATE(civicrm_event.end_date) BETWEEN %1 AND %2
            OR DATE(civicrm_event.start_date) <= %2
          )
        )
    ';

    if (!empty($eventTypeId)) {
      $whereCondition .= ' AND event_type_value.value IN (' . implode(', ', $eventTypeId) . ') ';
    }

    if (!empty($eventStatusId)) {
      $whereCondition .= ' AND civicrm_participant.status_id IN (' . implode(', ', $eventStatusId) . ') ';
    }

    $query .= $whereCondition;

    $query .= 'GROUP BY id';

    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [ $params['startDate'], 'String' ],
      2 => [ $params['endDate'], 'String' ]
    ]);

    while ($dao->fetch()) {
      $eventData = [
        'id' => $dao->id,
        'url' => htmlspecialchars_decode(CRM_Utils_System::url('civicrm/event/info', 'reset=1&id=' . $dao->id . '&cid=' . $dao->contact_id . '&action=view&context=participant&selectedChild=event')),
        'constraint' => TRUE,
        'participantRole' => self::getRoleById($dao->participantRoleId),
        'color' => self::EVENT_COLOR,
        'type' => self::TYPE_EVENTS,
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

  /**
   * Get all available participant status
   *
   * @return array
   */
  public static function getParticipantStatus() {
    $query = CRM_Utils_SQL_Select::from(CRM_Event_BAO_ParticipantStatusType::getTableName());
    $dao = CRM_Core_DAO::executeQuery($query->toSQL());

    $participantStatuses = [];

    while ($dao->fetch()) {
      $participantStatuses[$dao->id] = $dao->label;
    }

    return $participantStatuses;
  }

  /**
   * Gets role by id
   *
   * @param $roleId
   *
   * @return string
   *
   */
  public static function getRoleById($roleId) {
    preg_match_all('/\d+/', $roleId, $ids);

    $roleLabels = '';
    $countIds = count($ids[0]);

    for ($i = 0; $i < $countIds; $i++) {
      try {
        $role = civicrm_api3('OptionValue', 'getsingle', [
          'sequential' => 1,
          'option_group_id' => 'participant_role',
          'value' => $ids[0][$i],
        ]);
        $roleLabels .= $i == $countIds - 1 ? '<p>' . $role['label'] . '</p>' : '<p>' . $role['label'] . ';</p>';
      } catch (Exception $e) {
      }
    }

    return $roleLabels;
  }

}
