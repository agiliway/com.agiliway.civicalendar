<?php

/**
 * Gets calendar via API
 *
 * @param $params
 *
 * @return array
 * @throws \CRM_Core_Exception
 * @throws \CiviCRM_API3_Exception
 */
function civicrm_api3_calendar_get($params) {
  $calendarParams = [
    'hidePastEvents' => $params['hidePastEvents'],
    'startDate' => $params['start'],
    'endDate' => $params['end'],
    'type' => $params['type'],
  ];

  $eventsHandler = new CRM_Calendar_Common_Handler($params['contact_id'], $calendarParams);
  $events = $eventsHandler->getAllEvents();

  return civicrm_api3_create_success($events, $params);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_calendar_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['start'] = [
    'title' => 'Start date',
    'description' => ts('Start date for searching (Y-m-d H:i:s)'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_DATE,
  ];
  $params['end'] = [
    'title' => 'End  date',
    'description' => ts('End date for searching (Y-m-d H:i:s)'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_DATE,
  ];
  $params['type'] = [
    'title' => 'Type',
    'description' => ts('Event type'),
    'api.default' => ['all'],
    'type' => CRM_Utils_Type::T_STRING,
    'options' => [
      CRM_Calendar_Common_Handler::TYPE_ALL => CRM_Calendar_Common_Handler::TYPE_ALL,
      CRM_Calendar_Common_Event::TYPE_EVENTS => CRM_Calendar_Common_Event::TYPE_EVENTS,
      CRM_Calendar_Common_Case::TYPE_CASES => CRM_Calendar_Common_Case::TYPE_CASES,
      CRM_Calendar_Common_Activity::TYPE_ACTIVITIES => CRM_Calendar_Common_Activity::TYPE_ACTIVITIES,
    ],
  ];
}
