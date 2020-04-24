<?php

class CRM_Calendar_Utils_Locale {

  /**
   * Returns map of languages(format: civiCRM language => civiCalendar language)
   *
   * @return array
   */
  public static function getLocaleMap() {
    return [
      'default' => 'en',

      'af_ZA' => 'af',
      'sq_AL' => 'sq',
      'ar_EG' => 'ar',
      'bg_BG' => 'bg',
      'ca_ES' => 'ca',
      'zh_CN' => 'zh-cn',
      'zh_TW' => 'zh-tw',
      'cs_CZ' => 'cs',
      'da_DK' => 'da',
      'nl_NL' => 'nl',
      'en_AU' => 'en-au',
      'en_CA' => 'en-ca',
      'en_GB' => 'en-gb',
      'en_US' => 'en',
      'et_EE' => 'et',
      'fi_FI' => 'fi',
      'fr_CA' => 'fr-ca',
      'fr_FR' => 'fr',
      'de_DE' => 'de',
      'de_CH' => 'de-ch',
      'el_GR' => 'el',
      'he_IL' => 'he',
      'hi_IN' => 'hi',
      'hu_HU' => 'hu',
      'id_ID' => 'id',
      'it_IT' => 'it',
      'ja_JP' => 'ja',
      'lt_LT' => 'lt',
      'nb_NO' => 'nb',
      'fa_IR' => 'fa',
      'pl_PL' => 'pl',
      'pt_BR' => 'pt-br',
      'pt_PT' => 'pt',
      'ro_RO' => 'ro',
      'ru_RU' => 'ru',
      'sr_RS' => 'sr',
      'sk_SK' => 'sk',
      'sl_SI' => 'sl',
      'es_ES' => 'es',
      'sv_SE' => 'sv',
      'th_TH' => 'th',
      'tr_TR' => 'tr',
      'uk_UA' => 'uk',
      'vi_VN' => 'vi',
    ];
  }

  /**
   * Returns language for civiCalendar by site language
   *
   * @return string
   */
  public static function getLocaleForCalendar() {
    $civicrmLocale = CRM_Core_I18n::getLocale();
    $localeMap = self::getLocaleMap();
    return isset($localeMap[$civicrmLocale]) ? $localeMap[$civicrmLocale] : $localeMap['default'];
  }

  /**
   * Returns language for civiCalendar by current language
   *
   * @return string
   */
  public static function getCurrentLocaleForCalendar() {
    $session = CRM_Core_Session::singleton();
    $sessionLocale = $session->get('lcMessages');
    $localeMap = self::getLocaleMap();
    return isset($localeMap[$sessionLocale]) ? $localeMap[$sessionLocale] : self::getLocaleForCalendar();
  }

}
