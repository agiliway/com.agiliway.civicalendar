<?php

class CRM_Calendar_Utils_Extension {

  /**
   * Getting image path
   *
   * @return mixed
   */
  public static function getImagePath() {
    return str_replace($_SERVER['DOCUMENT_ROOT'], '', CRM_Core_Config::singleton()->extensionsDir . CRM_Calendar_ExtensionUtil::LONG_NAME . '/img/');
  }

  /**
   * @param $contactType
   *
   * @return string
   */
  public static function getDefaultImageUrl($contactType) {
    $imagePath = CRM_Calendar_Utils_Extension::getImagePath();
    if ($contactType == 'Individual') {
      $imageUrl = $imagePath . 'Person.svg';
    } elseif ($contactType == 'Organization') {
      $imageUrl = $imagePath . 'Organization.svg';
    } else {
      $imageUrl = $imagePath . 'Person.svg';
    }

    return $imageUrl;
  }

  /**
   * Getting list of CiviCRM enabled components
   *
   * @return array|bool
   */
  public static function getEnabledComponents() {
    $enabledComponents = CRM_Core_Component::getEnabledComponents();
    if (!empty($enabledComponents) && is_array($enabledComponents)) {
      return array_keys($enabledComponents);
    }

    return [];
  }

  /**
   *Save calendar settings when calendar upgrade settings key names
   */
  public static function saveUpgradingSettings() {
    $allSettings = civicrm_api3('Setting', 'getsingle', ['sequential' => 1,]);
    $neededSettings = [];

    if (isset($allSettings['civicalendar_activitytypes'])) {
      $neededSettings['civicalendar_activity_types'] = $allSettings['civicalendar_activitytypes'];
    }
    if (isset($allSettings['civicalendar_scrolltime'])) {
      $neededSettings['civicalendar_scroll_time'] = $allSettings['civicalendar_scrolltime'];
    }
    if (isset($allSettings['civicalendar_defaultview'])) {
      $neededSettings['civicalendar_default_view'] = $allSettings['civicalendar_defaultview'];
    }
    if (isset($allSettings['civicalendar_includecontactnamesintitle'])) {
      $neededSettings['civicalendar_include_contact_names_in_title'] = $allSettings['civicalendar_includecontactnamesintitle'];
    }
    if (isset($allSettings['civicalendar_hidepastevents'])) {
      $neededSettings['civicalendar_hide_past_events'] = $allSettings['civicalendar_hidepastevents'];
    }
    if ($allSettings['civicalendar_timeformat'] == '(h:mm)t' || $allSettings['civicalendar_timeformat'] == 'H:mm') {
      $neededSettings['civicalendar_time_format'] = $allSettings['civicalendar_timeformat'];
    }
    else {
      $neededSettings['civicalendar_time_format'] = '(h:mm)t';
    }

    CRM_Core_Invoke::rebuildMenuAndCaches(TRUE);

    civicrm_api3('Setting', 'create', $neededSettings);
  }

}
