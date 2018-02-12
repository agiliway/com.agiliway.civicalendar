<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Calendar_Upgrader extends CRM_Calendar_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  public function install() {
    $this->executeSqlFile('sql/install.sql');
  }

   /**
   * Example: Run an external SQL script when the module is uninstalled.
   */
  public function uninstall() {
   $this->executeSqlFile('sql/uninstall.sql');
  }

  /**
   * Example: Run a simple query when a module is enabled.
   */
  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE civicrm_dashboard SET is_active = 1 WHERE name = "calendar"');
  }

  /**
   * Example: Run a simple query when a module is disabled.
   */
  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE civicrm_dashboard SET is_active = 0 WHERE name = "calendar"');
  }

}
