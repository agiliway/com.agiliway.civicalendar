<?php

require_once 'calendar.civix.php';
use CRM_Calendar_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function calendar_civicrm_config(&$config)
{
  _calendar_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function calendar_civicrm_xmlMenu(&$files)
{
  _calendar_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function calendar_civicrm_install()
{
  _calendar_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function calendar_civicrm_postInstall()
{
  _calendar_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function calendar_civicrm_uninstall()
{
  _calendar_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function calendar_civicrm_enable()
{
  _calendar_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function calendar_civicrm_disable()
{
  _calendar_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function calendar_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL)
{
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
function calendar_civicrm_managed(&$entities)
{
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
function calendar_civicrm_caseTypes(&$caseTypes)
{
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
function calendar_civicrm_angularModules(&$angularModules)
{
  _calendar_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function calendar_civicrm_alterSettingsFolders(&$metaDataFolders = NULL)
{
  _calendar_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementatio of hook__civicrm_tabs
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tabs
 */
function calendar_civicrm_tabs(&$allTabs, $contactID = NULL)
{
  if ($contactID) {
    $url = CRM_Utils_System::url('civicrm/calendar', 'reset=1&cid=' . $contactID);
  }
  else {
    $url = CRM_Utils_System::url('civicrm/calendar', '');
  }

  $newTab = array(
    'id' => 'calendar',
    'url' => $url,
    'title' => ts('Calendar'),
    'weight' => 15
  );

  array_push($allTabs, $newTab);

  _calendar_civix_addJSCss();
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
 */
function calendar_civicrm_navigationMenu(&$menu) {
  $item[] =  array (
    'name'       => 'Calendar Settings',
    'url'        => 'civicrm/admin/calendar',
    'permission' => 'administer CiviCRM',
    'operator'   => NULL,
    'separator'  => NULL,
  );
  _calendar_civix_insert_navigation_menu($menu, 'Administer/Customize Data and Screens', $item[0]);
}

function calendar_civicrm_dashboard($contactID, &$contentPlacement)
{
    $session = CRM_Core_Session::singleton();
    $contactID = $session->get('userID');
    
    $isOnDashboard = CRM_Core_DAO::singleValueQuery("
        SELECT count(*) FROM civicrm_dashboard
        WHERE name='calendar' and is_active=1 and domain_id in (select id from civicrm_domain where name = 'Default Domain Name')
      ");
    if($isOnDashboard){
        _calendar_civix_addJSCss();
    }
    return '';
} 

function  _calendar_civix_addJSCss()
{
    CRM_Core_Resources::singleton()->addStyleFile('com.agiliway.civicalendar', 'css/fullcalendar.min.css', 200, 'html-header');
    CRM_Core_Resources::singleton()->addStyleFile('com.agiliway.civicalendar', 'css/calendar.css', 201, 'html-header');
    CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'js/moment.min.js', 200, 'html-header');
    CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'js/fullcalendar.min.js', 201, 'html-header');
    CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'js/locale-all.js', 201, 'html-header');
    CRM_Core_Resources::singleton()->addScriptFile('com.agiliway.civicalendar', 'locale/' . CRM_Calendar_Settings::getValue('locale') . '.js', 202, 'html-header');
}