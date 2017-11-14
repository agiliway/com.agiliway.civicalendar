<?php

class CRM_Calendar_Page_Calendar extends CRM_Core_Page
{

  public function run()
  {
    if (!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      CRM_Core_Resources::singleton()->addStyleFile('com.agiliway.civicalendar', 'css/fullcalendar.min.css', 200, 'html-header');
      CRM_Core_Resources::singleton()->addStyleFile('com.agiliway.civicalendar', 'css/calendar.css', 201, 'html-header');
      CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'js/moment.min.js', 200, 'html-header');
      CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'js/fullcalendar.min.js', 201, 'html-header');
      CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'locale/' . _calendar_civicrm_getSetting()['lang'] . '.js', 202, 'html-header');
    }

    CRM_Utils_System::setTitle(ts('Calendar'));
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, true);

    $this->assign('settings', _calendar_civicrm_getSetting());
    $this->assign('contactId', $this->_contactId);

    parent::run();
  }

}