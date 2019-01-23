<?php

class CRM_Calendar_Settings {

  /**
   * Extension system title
   */
  const TITLE = 'CiviCalendar';

  /**
   * @var boolean
   *  Flag to see if Extension Shoreditch is active
   */
  private static $isShoreditch;

  /**
   * Get settings prefix name for this extension
   *
   * @return string
   */
  public static function getPrefix() {
    return 'civicalendar_';
  }

  /**
   * Get filter of valid settings for this extension
   *
   * @return array
   */
  public static function getFilter() {
    return ['group' => 'civicalendar'];
  }

  /**
   * Get name of setting
   *
   * @param $name
   * @param bool $prefix
   *
   * @return mixed|string
   */
  public static function getName($name, $prefix = FALSE) {
    $prepareName = str_replace(self::getPrefix(), '', $name);

    if ($prefix) {
      $prepareName = self::getPrefix() . $prepareName;
    }

    return $prepareName;
  }

  /**
   * Save settings. Accepts an array of name=>value pairs.  Name can be with or
   * without prefix (it will be added if missing).
   *
   * @param $settings
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function save($settings) {
    foreach ($settings as $name => $value) {
      $prefixedSettings[self::getName($name, TRUE)] = $value;
    }

    civicrm_api3('setting', 'create', $prefixedSettings);
  }

  /**
   * Read setting that has prefix in database and return single value
   *
   * @param $name
   *
   * @return mixed
   * @throws \CiviCRM_API3_Exception
   */
  public static function getValue($name) {
    $settings = civicrm_api3('setting', 'get', ['return' => CRM_Calendar_Settings::getName($name, TRUE)]);
    $domainID = CRM_Core_Config::domainID();

    if (isset($settings['values'][$domainID][CRM_Calendar_Settings::getName($name, TRUE)])) {
      return $settings['values'][$domainID][CRM_Calendar_Settings::getName($name, TRUE)];
    }

    return '';
  }

  /**
   * Get settings
   *
   * @param array $settings of settings (eg. array(username, password))
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function get($settings) {
    $domainID = CRM_Core_Config::domainID();

    foreach ($settings as $name) {
      $prefixedSettings[] = self::getName($name, TRUE);
    }

    $settingsResult = civicrm_api3('setting', 'get', ['return' => $prefixedSettings]);

    if (isset($settingsResult['values'][$domainID])) {
      foreach ($settingsResult['values'][$domainID] as $name => $value) {
        $nonPrefixedSettings[self::getName($name)] = $value;
      }

      return empty($nonPrefixedSettings) ? NULL : $nonPrefixedSettings;
    }

    return [];
  }

  /**
   * @return bool
   * @throws \CiviCRM_API3_Exception
   */
  public static function isShoreditch() {
    if (is_null(self::$isShoreditch)) {
      $shoreditchExtensionCount = civicrm_api3('Extension', 'getcount', [
        'full_name' => 'org.civicrm.shoreditch',
        'is_active' => 1,
      ]);

      self::$isShoreditch = !empty(Civi::settings()->get('customCSSURL')) && $shoreditchExtensionCount == 1;
    }

    return self::$isShoreditch;
  }
}
