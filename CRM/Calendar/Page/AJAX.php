<?php

class CRM_Calendar_Page_AJAX {

  /**
   * Ajax called, gets all contact events, as json
   *
   * @return void
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public static function getContactEvents() {
    $contactId = CRM_Utils_Request::retrieve('cid', 'Positive');

    if (empty($contactId)) {
      $session = CRM_Core_Session::singleton();
      $userID = $session->get('userID');
      $contactId = [$userID];
    }

    $params = [
      'hidePastEvents' => CRM_Calendar_Settings::getValue('hidepastevents'),
      'startDate' => gmdate('Y-m-d H:i:s', CRM_Utils_Request::retrieve('start', 'String')),
      'endDate' => gmdate('Y-m-d H:i:s', CRM_Utils_Request::retrieve('end', 'String')),
    ];

    $eventsHandler = new CRM_Calendar_Common_Handler($contactId, $params);
    $events = $eventsHandler->getAllEventsOld();

    CRM_Utils_JSON::output($events);
  }

  /**
   * Ajax called, gets all contacts events, as json, for Contact calendar
   * sharing
   *
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public static function getContactEventOverlaying() {
    $contactId = CRM_Utils_Request::retrieve('cid', 'Memo');

    if (empty($contactId)) {
      $contactId = 0;
    }

    $params = [
      'hidePastEvents' => CRM_Calendar_Settings::getValue('hidepastevents'),
      'startDate' => gmdate('Y-m-d H:i:s', CRM_Utils_Request::retrieve('start', 'String')),
      'endDate' => gmdate('Y-m-d H:i:s', CRM_Utils_Request::retrieve('end', 'String')),
    ];

    $eventsHandler = new CRM_Calendar_Common_Handler($contactId, $params);
    $events = $eventsHandler->getAllEventsOld();

    CRM_Utils_JSON::output($events);
  }

}
