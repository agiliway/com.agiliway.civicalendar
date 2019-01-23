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
   */
  public static function getEvents($contactIds, $params, $fields) {
    $eventTypeId = CRM_Utils_Request::retrieve('eventTypeId', 'String');
    $eventStatusId = CRM_Utils_Request::retrieve('eventStatusId', 'String');
    $imagePath = CRM_Calendar_Common_Handler::getImagePath();

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
        civicrm_participant.contact_id,
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

    $whereCondition = ' 
      AND civicrm_event.is_active = 1 
      AND civicrm_event.is_template = 0
      AND ( civicrm_participant.contact_id IN (' . implode(', ', $contactIds) . ') OR  civicrm_event.created_id IN(' . implode(', ', $contactIds) . '))
      AND NOT
        (
          (
            DATE(civicrm_event.start_date) BETWEEN "' . $params['startDate'] . '" AND "' . $params['endDate'] . '"
            OR DATE(civicrm_event.end_date) >= "' . $params['startDate'] . '"
          )
          XOR
          (
            DATE(civicrm_event.end_date) BETWEEN "' . $params['startDate'] . '" AND "' . $params['endDate'] . '"
            OR DATE(civicrm_event.start_date) <= "' . $params['endDate'] . '"
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

    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      $eventData = [];

      foreach ($fields as $k) {
        if (isset($dao->$k)) {
          $eventData[$k] = $dao->$k;
        }
      }

      $eventData['id'] = $dao->event_id;
      $eventData['url'] = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/event/info', 'reset=1&id=' . $dao->id . '&cid=' . $dao->contact_id . '&action=view&context=participant&selectedChild=event'));
      $eventData['constraint'] = TRUE;
      $eventData['participantRole'] = self::getRoleById($eventData['participantRoleId']);
      $eventData['color'] = self::EVENT_COLOR;
      $eventData['type'] = self::TYPE_EVENTS;
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
