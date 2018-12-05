<?php

/**
 * Calendar dashlet for Main page
 */
class CRM_Dashlet_Page_Calendar extends CRM_Core_Page {

  public $enabledComponents;

  /**
   * List activities as dashlet
   *
   * @return void
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  function __construct($title = NULL, $mode = NULL) {
    parent::__construct($title, $mode);

    $this->enabledComponents = CRM_Calendar_Common_Handler::getEnabledComponemnts();
  }

  public function run() {

    if (!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      _calendar_civix_addJSCss();
    }

    if (in_array('CiviEvent', $this->enabledComponents)) {
      $this->assign('event_is_enabled', TRUE);
    }

    if (in_array('CiviCase', $this->enabledComponents)) {
      $this->assign('case_is_enabled', TRUE);
    }

    $context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'dashlet');
    $this->assign('context', $context);

    $settings = CRM_Calendar_Settings::get([
      'scrolltime',
      'defaultview',
      'dayofmonthformat',
      'timeformat',
      'hidepastevents',
      'locale',
      'height',
    ]);
    $settings['scrollTime'] = $settings['scrolltime'];
    $settings['defaultView'] = $settings['defaultview'];
    $settings['dayOfMonthFormat'] = $settings['dayofmonthformat'];
    $settings['timeFormat'] = $settings['timeformat'];
    $settings['hidePastEvents'] = $settings['hidepastevents'];
    $settings['locale'] = $settings['locale'];
    $settings['height'] = $settings['height'];
    $this->assign('settings', $settings);

    $session = CRM_Core_Session::singleton();
    $contactID = $session->get('userID');

    $this->assign('contactId', $contactID);

    return parent::run();
  }

}
