<?php

require_once 'calendar.civix.php';

use CRM_Calendar_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function calendar_civicrm_config(&$config) {
  _calendar_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function calendar_civicrm_xmlMenu(&$files) {
  _calendar_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function calendar_civicrm_install() {
  _calendar_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function calendar_civicrm_postInstall() {
  _calendar_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function calendar_civicrm_uninstall() {
  _calendar_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function calendar_civicrm_enable() {
  _calendar_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function calendar_civicrm_disable() {
  _calendar_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function calendar_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _calendar_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function calendar_civicrm_managed(&$entities) {
  _calendar_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function calendar_civicrm_caseTypes(&$caseTypes) {
  _calendar_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function calendar_civicrm_angularModules(&$angularModules) {
  _calendar_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function calendar_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _calendar_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_tabset().
 */
function calendar_civicrm_tabset($path, &$tabs, $context) {
  if ($path === 'civicrm/contact/view') {
    $url = CRM_Utils_System::url('civicrm/calendar', ['cid' => $context['contact_id']]);

    $tabs[] = [
      'id' => 'calendar',
      'url' => $url,
      'title' => E::ts('Calendar'),
      'weight' => 15,
      'icon' => 'crm-i fa-calendar-check-o',
    ];

    _calendar_civix_addJSCss();
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
 */
function calendar_civicrm_navigationMenu(&$menu) {
  $civiCalendarSettings = [
    'name' => 'Calendar Settings',
    'url' => 'civicrm/admin/calendar',
    'permission' => 'administer CiviCRM',
    'operator' => NULL,
    'separator' => NULL,
  ];
  _calendar_civix_insert_navigation_menu($menu, 'Administer/', $civiCalendarSettings);
}

/**
 * @param $contactID
 * @param $contentPlacement
 *
 * @return string
 */
function calendar_civicrm_dashboard($contactID, &$contentPlacement) {
  $isOnDashboard = CRM_Core_DAO::singleValueQuery('
        SELECT count(*) 
        FROM civicrm_dashboard
        WHERE `name` = "calendar" 
        AND is_active=1 
        AND domain_id IN (select MIN(id) from civicrm_domain)
      ');

  if ($isOnDashboard) {
    _calendar_civix_addJSCss();
  }

  return '';
}

/**
 * Implements hook_civicrm_pageRun().
 */
function calendar_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');

  if ($pageName == 'CRM_Event_Page_EventInfo' && CRM_Core_Permission::check('register for events')) {
    CRM_Core_Region::instance('page-body')->add([
      'template' => CRM_Calendar_ExtensionUtil::path() . '/templates/CRM/Calendar/Page/Field/EventInfo.tpl'
    ]);
  }
}

/**
 * Adding css and js files to page body
 */
function _calendar_civix_addJSCss() {
  CRM_Core_Resources::singleton()
    ->addStyleFile('com.agiliway.civicalendar', 'css/fullcalendar.min.css', 200, 'html-header');
  CRM_Core_Resources::singleton()
    ->addStyleFile('com.agiliway.civicalendar', 'css/calendar.css', 201, 'html-header');

  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/tooltip.js', 199, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/popover.js', 200, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/moment.min.js', 200, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/fullcalendar.min.js', 201, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/locale-all.js', 201, 'html-header');

  if (CRM_Calendar_Settings::isShoreditch()) {
    CRM_Core_Resources::singleton()
      ->addStyleFile('com.agiliway.civicalendar', 'css/shoreditch-fix.css', 199, 'html-header');
  }

  CRM_Core_Resources::singleton()->addVars('datepicker_locale', ['language' => CRM_Calendar_Utils_Locale::getCurrentLocaleForCalendar()]);
  CRM_Core_Region::instance('page-header')->add([
    'scriptUrl' => CRM_Calendar_ExtensionUtil::url('js/DatepickerFix.js'),
  ]);
}
