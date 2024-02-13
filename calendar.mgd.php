<?php

// phpcs:disable
use CRM_Calendar_ExtensionUtil as E;
// phpcs:enable

// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'calendar',
    'module' => 'com.agiliway.civicalendar',
    'entity' => 'dashboard',
    'params' => [
      'version' => 3,
      'domain_id' => CRM_Core_Config::domainID(),
      'label' => E::ts('Calendar'),
      'name' => 'calendar',
      'url' => 'civicrm/dashlet/calendar?reset=1',
      'permission' => 'access CiviCRM',
      'fullscreen_url' => 'civicrm/dashlet/calendar?reset=1&context=dashletFullscreen',
      'is_active' => 1,
      'is_reserved' => 1,
      'cache_minutes' => 60,
    ],
  ],
];
