<?php

return array(
  'scrollTime' => array('default' => '6:00'),
  'defaultView' => array('default' => 'month'),
  'dayOfMonthFormat' => array('default' =>'ddd DD'),
  'hidePastEvents' => array('default' => '0'),
  'lang' => array('default' => CRM_Core_I18n::getLocale()),  
  'height' => array('default' => '500'),


  'civicalendar_scrolltime' => array(
    'group_name' => 'CiviCalendar Settings',
    'group' => 'civicalendar',
    'name' => 'civicalendar_scrolltime',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => '6:00:00',
    'description' => ts('Scroll pane is initially scrolled down to 6:00 AM'),
    'html_type' => 'Select',
    'html_attributes' => array(
      'size' => 20,
    ),
    'option_values' => array(
      '12:00:00' => '12:00 am',
      '1:00:00' => '1:00 am',
      '2:00:00' => '2:00 am',
      '3:00:00' => '3:00 am',
      '4:00:00' => '4:00 am',
      '5:00:00' => '5:00 am',
      '6:00:00' => '6:00 am',
      '7:00:00' => '7:00 am',
      '8:00:00' => '8:00 am',
      '9:00:00' => '9:00 am',
      '10:00:00' => '10:00 am',
    ),
  ),

  'civicalendar_defaultview' => array(
    'group_name' => 'CiviCalendar Settings',
    'group' => 'civicalendar',
    'name' => 'civicalendar_defaultview',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => 'month',
    'description' => ts('Default tab for calendar (month, week, day)'),
    'html_type' => 'Select',
    'html_attributes' => array(
      'size' => 20,
    ),
    'option_values' => array(
      'month' => ts('month'),
      'agendaWeek' => ts('week'),
      'agendaDay' => ts('day'), 
    ),
  ),
 
  'civicalendar_dayofmonthformat' => array(
    'group_name' => 'CiviCalendar Settings',
    'group' => 'civicalendar',
    'name' => 'civicalendar_dayofmonthformat',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => 'ddd DD',
    'description' => ts('Day of Month Format (eg. ddd DD)'),
    'html_type' => 'Text',
    'html_attributes' => array(
      'size' => 20,
    ),
  ),

  'civicalendar_hidepastevents' => array(
    'group_name' => 'CiviCalendar Settings',
    'group' => 'civicalendar',
    'name' => 'civicalendar_hidepastevents',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => '0',
    'description' => ts('Hide past events'),
    'html_type' => 'Checkbox',
  ),

  'civicalendar_lang' => array(
    'group_name' => 'CiviCalendar Settings',
    'group' => 'civicalendar',
    'name' => 'civicalendar_lang',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => CRM_Core_I18n::getLocale(),
    'description' => ts('Locale (eg. en_US)'),
    'html_type' => 'Text',
    'html_attributes' => array(
      'size' => 20,
    ),
  ),

  'civicalendar_height' => array(
    'group_name' => 'CiviCalendar Settings',
    'group' => 'civicalendar',
    'name' => 'civicalendar_height',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => '500',
    'description' => ts('Height (eg. 500)'),
    'html_type' => 'Text',
    'html_attributes' => array(
      'size' => 20,
    ),
  ),
);