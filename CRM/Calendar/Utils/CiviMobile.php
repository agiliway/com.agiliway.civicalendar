<?php

class CRM_Calendar_Utils_CiviMobile {

  /**
   * Is "com.agiliway.civimobileapi" installed
   *
   * @return bool
   */
  public static function isCiviMobileApiEnable() {
    try {
      $extensionStatus = civicrm_api3('Extension', 'getsingle', [
        'return' => "status",
        'full_name' => "com.agiliway.civimobileapi",
      ]);
    } catch (Exception $e) {
      return FALSE;
    }

    if ($extensionStatus['status'] == 'installed') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Is checked 'synchronize_with_civicalendar' setting option in CiviMobile
   *
   * @return bool
   */
  public static function isActivateCiviCalendarSettings() {
    try {
      $civiCalendarSetting = civicrm_api3('Setting', 'getsingle', [
        'return' => 'civimobileapi_calendar_synchronize_with_civicalendar'
      ]);
    } catch (Exception $e) {
      return FALSE;
    }

    if (empty($civiCalendarSetting['civimobileapi_calendar_synchronize_with_civicalendar'])) {
      return FALSE;
    }

    if ($civiCalendarSetting['civimobileapi_calendar_synchronize_with_civicalendar'] == 1) {
      return TRUE;
    }

    return FALSE;
  }

}
