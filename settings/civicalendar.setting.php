<?php

return array(
  'dayOfMonthFormat' => array('default' =>'ddd DD'),
  'defaultView' => array('default' => 'month'),
  'height' => array('default' => '500'),
  'lang' => array('default' => CRM_Core_I18n::getLocale()),

  'civicalendar_dayofmonthformat' => array(
    'group_name' => 'CiviCalendar Settings',
    'group' => 'civicalendar',
    'name' => 'civicalendar_dayofmonthformat',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => 'ddd DD',
    'description' => 'Day of Month Format (eg. ddd DD)',
    'html_type' => 'Text',
    'html_attributes' => array(
      'size' => 50,
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
    'description' => 'Default View (eg. month)',
    'html_type' => 'Text',
    'html_attributes' => array(
      'size' => 50,
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
    'description' => 'Height (eg. 500)',
    'html_type' => 'Text',
    'html_attributes' => array(
      'size' => 50,
    ),
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
    'description' => 'Locale (eg. en_US)',
    'html_type' => 'Text',
    'html_attributes' => array(
      'size' => 50,
    ),
  ),
);