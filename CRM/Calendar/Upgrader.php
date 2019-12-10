<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Calendar_Upgrader extends CRM_Calendar_Upgrader_Base {

  public function upgrade_0001() {
    $this->ctx->log->info('Applying update 0001');
    CRM_Calendar_Utils_Extension::saveUpgradingSettings();

    return TRUE;
  }

  public function install() {
    $this->executeSqlFile('sql/install.sql');
    $this->addMenuItems();
  }

  public function uninstall() {
    $this->executeSqlFile('sql/uninstall.sql');
    $this->deleteMenuItems();
  }

  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE civicrm_dashboard SET is_active = 1 WHERE `name` = "calendar"');
    CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE `name` = "Contact calendar sharing"');
  }

  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE civicrm_dashboard SET is_active = 0 WHERE `name` = "calendar"');
    CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE `name` = "Contact calendar sharing"');
  }

  /**
   * Add menu items for calendar to menu
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function addMenuItems() {
    $resultNavigationCalendar = civicrm_api3('Navigation', 'get', [
      'sequential' => 1,
      'return' => ['id'],
      'is_active' => 1,
      'label' => ts('Contact calendar sharing'),
    ]);

    if ($resultNavigationCalendar['count'] > 0){
      return ;
    }

    $resultNavigation = civicrm_api3('Navigation', 'get', [
      'sequential' => 1,
      'return' => ['id'],
      'is_active' => 1,
      'label' => ts('Contacts'),
    ]);

    if ($resultNavigation['count'] == 1) {
      $parentId = $resultNavigation['values'][0]['id'];

      $navigation = [
        'label' => ts('Contact calendar sharing'),
        'name' => 'Contact calendar sharing',
        'url' => 'civicrm/calendar/overlaying',
        'permission' => 'access CiviCRM',
        'parent_id' => $parentId,
        'is_active' => TRUE,
      ];

      CRM_Core_BAO_Navigation::add($navigation);
    }
  }

  /**
   * Delete calendar menu items
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function deleteMenuItems() {
    $resultNavigation = civicrm_api3('Navigation', 'get', [
      'sequential' => 1,
      'return' => ['id'],
      'name' => ts('Contact calendar sharing'),
    ]);

    if ($resultNavigation['count'] == 1) {
      $itemId = $resultNavigation['values'][0]['id'];

      civicrm_api3('Navigation', 'delete', [
        'id' => $itemId,
      ]);
    }
  }

  /**
   * Gets extension dir
   *
   * @return string
   */
  public function getExtensionDir() {
    return $this->extensionDir;
  }

}
