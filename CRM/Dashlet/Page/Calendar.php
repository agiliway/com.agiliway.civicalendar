<?php

/**
 * Calendar dashlet for Main page
 */
class CRM_Dashlet_Page_Calendar extends CRM_Core_Page {

  public function run() {
    if (!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      _calendar_civix_addJSCss();
    }
    $enabledComponents = CRM_Calendar_Utils_Extension::getEnabledComponents();

    if (in_array('CiviEvent', $enabledComponents)) {
      $this->assign('event_is_enabled', TRUE);
    }

    if (in_array('CiviCase', $enabledComponents)) {
      $this->assign('case_is_enabled', TRUE);
    }

    $context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'dashlet');
    $this->assign('context', $context);

    $settings = CRM_Calendar_Settings::get([
      'scroll_time',
      'default_view',
      'time_format',
      'hide_past_events',
    ]);
    $settings['scrollTime'] = $settings['scroll_time'];
    $settings['defaultView'] = $settings['default_view'];
    $settings['timeFormat'] = $settings['time_format'];
    $settings['hidePastEvents'] = $settings['hide_past_events'];
    $settings['locale'] = CRM_Calendar_Utils_Locale::getCurrentLocaleForCalendar();
    $settings['languageMap'] = json_encode(CRM_Calendar_Utils_Locale::getLocaleMap());
    $this->assign('settings', $settings);

    $session = CRM_Core_Session::singleton();
    $contactID = $session->get('userID');

    $this->assign('contactId', $contactID);

    return parent::run();
  }

}
