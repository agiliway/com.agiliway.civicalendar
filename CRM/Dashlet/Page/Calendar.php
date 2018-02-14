<?php

/**
 * Main page for Calendar dashlet
 *
 */
class CRM_Dashlet_Page_Calendar extends CRM_Core_Page {

  /**
   * List activities as dashlet.
   *
   * @return void
   */
  public function run() {
     
    $context = CRM_Utils_Request::retrieve('context', 'String', $this, FALSE, 'dashlet');
    $this->assign('context', $context);
    
    $settings = CRM_Calendar_Settings::get(array ('scrolltime', 'defaultview', 'dayofmonthformat', 'timeformat', 'hidepastevents', 'locale', 'height'));
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
