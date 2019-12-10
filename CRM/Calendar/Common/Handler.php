<?php

class CRM_Calendar_Common_Handler {

  /**
   * All types of events, for filter
   */
  const TYPE_ALL = 'all';

  /**
   * Contact Id can be integer or array
   *
   * @var array
   */
  private $contactIds;

  /**
   * Form fields values
   *
   * @var array
   */
  private $params;

  /**
   * Response fields
   *
   * @var array
   */
  private $fields = [
    'title' => 'title',
    'start' => 'start',
    'url' => 'url',
    'end' => 'end',
    'id' => 'id',
    'activityType' => 'activityType',
    'eventType' => 'eventType',
    'participantRoleId' => 'participantRoleId',
    'priority' => 'priority',
    'assignContact' => 'assignContact',
    'type' => 'type',
  ];

  /**
   * List of enabled civicrm components
   *
   * @var array|bool
   */
  public $enabledComponents;

  /**
   * @param $contactIds
   * @param $params
   */
  public function __construct($contactIds, $params) {
    if (!is_array($contactIds)) {
      $contactIds = [$contactIds];
    }

    if (count($contactIds) > 1) {
      $contactIds = array_filter($contactIds);
    }

    $this->contactIds = $contactIds;
    $this->enabledComponents = CRM_Calendar_Utils_Extension::getEnabledComponents();
    $this->params = $this->validateParams($params);
  }

  /**
   * Validate form params
   *
   * @param $params
   *
   * @return array
   */
  private function validateParams($params) {
    if (empty($params['startDate'])) {
      $params['startDate'] = gmdate('Y-m-d H:i:s', time());
    }

    if (empty($params['startDate'])) {
      $params['endDate'] = gmdate('Y-m-d H:i:s', time() + 86400);
    }

    if (empty($params['hide_past_events'])) {
      $params['hide_past_events'] = FALSE;
    }

    if (in_array('CiviEvent', $this->enabledComponents) && empty($params['eventColor'])) {
      $params['eventColor'] = '#35D0AE';
    }

    if (in_array('CiviCase', $this->enabledComponents) && empty($params['caseColor'])) {
      $params['caseColor'] = '#ff0000';
    }

    if (empty($params['activityColor'])) {
      $params['activityColor'] = '#F7CF5D';
    }

    if (!empty($params['type'])) {
      $this->params['type'][] = self::TYPE_ALL;
    }

    if (!empty($params['fields'])) {
      $this->fields = $params['fields'];
    }

    return $params;
  }

  /**
   * Get all type events
   *
   * @return array
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public function getAllEventsWeb() {
    if (in_array('CiviEvent', $this->enabledComponents)) {
      $events['events'] = CRM_Calendar_Common_Event::getEvents($this->contactIds, $this->params, $this->fields);
    }

    if (in_array('CiviCase', $this->enabledComponents)) {
      $events['case'] = CRM_Calendar_Common_Case::getCases($this->contactIds, $this->params, $this->fields);
    }

    $events['activity'] = CRM_Calendar_Common_Activity::getActivities($this->contactIds, $this->params, $this->fields);

    return $events;
  }

  /**
   * Get all type events
   *
   * @return array
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public function getAllEventsApi() {
    $events = [];

    if (in_array('CiviEvent', $this->enabledComponents)) {
      if (in_array(CRM_Calendar_Common_Event::TYPE_EVENTS, $this->params['type']) || in_array(self::TYPE_ALL, $this->params['type'])) {
        $events = array_merge($events, CRM_Calendar_Common_Event::getEvents($this->contactIds, $this->params, $this->fields));
      }
    }

    if (in_array('CiviCase', $this->enabledComponents)) {
      if (in_array(CRM_Calendar_Common_Case::TYPE_CASES, $this->params['type']) || in_array(self::TYPE_ALL, $this->params['type'])) {
        $events = array_merge($events, CRM_Calendar_Common_Case::getCases($this->contactIds, $this->params, $this->fields));
      }
    }

    if (in_array(CRM_Calendar_Common_Activity::TYPE_ACTIVITIES, $this->params['type']) || in_array(self::TYPE_ALL, $this->params['type'])) {
      $events = array_merge($events, CRM_Calendar_Common_Activity::getActivities($this->contactIds, $this->params, $this->fields));
    }

    return $events;
  }

}
