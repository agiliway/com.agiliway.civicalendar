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
      CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'js/locale-all.js', 201, 'html-header');
      CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'locale/' . CRM_Calendar_Settings::getValue('locale') . '.js', 202, 'html-header');
  }

    CRM_Utils_System::setTitle(ts('Calendar'));
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, true);

    $settings = CRM_Calendar_Settings::get(array ('scrolltime', 'defaultview', 'dayofmonthformat', 'timeformat', 'hidepastevents', 'locale', 'height'));
    $settings['scrollTime'] = $settings['scrolltime'];
    $settings['defaultView'] = $settings['defaultview'];
    $settings['dayOfMonthFormat'] = $settings['dayofmonthformat'];
    $settings['timeFormat'] = $settings['timeformat'];
    $settings['hidePastEvents'] = $settings['hidepastevents'];
    $settings['locale'] = $settings['locale'];
    $settings['height'] = $settings['height'];

    $this->assign('settings', $settings);
    $this->assign('contactId', $this->_contactId);

    parent::run();
  }

}