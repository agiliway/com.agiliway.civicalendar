<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Calendar_Upgrader extends CRM_Calendar_Upgrader_Base {

  public function install() {
    $this->executeSqlFile('sql/install.sql');
    $this->addMenuItems();
    $this->createActivityVisibilityOptionGroup();
  }

  public function uninstall() {
    $this->executeSqlFile('sql/uninstall.sql');
    $this->deleteMenuItems();
    $this->deleteActivityVisibilityOptionGroup();
  }

  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE civicrm_dashboard SET is_active = 1 WHERE `name` = "calendar"');
    CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 1 WHERE `name` = "Contact calendar sharing"');
  }

  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE civicrm_dashboard SET is_active = 0 WHERE `name` = "calendar"');
    CRM_Core_DAO::executeQuery('UPDATE civicrm_navigation SET is_active = 0 WHERE `name` = "Contact calendar sharing"');
  }

  public function upgrade_1001() {
    $this->createActivityVisibilityOptionGroup();

    return TRUE;
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

  /**
   * Create Activity Visibility OptionGroup
   */
  private function createActivityVisibilityOptionGroup() {
    $optionGroupParam = [
      'name'                   => 'activity_visibility',
      'title'                  => ts('Activity Visibility'),
      'sequential'             => 1,
      'is_reserved'            => 1,
      'is_active'              => 1,
      'api.OptionValue.create' => [
        [
          'label' => ts('Hidden'),
          'value' => 0,
          'name'  => 'hidden',
        ],
        [
          'label' => ts('Visibility'),
          'value' => 1,
          'name'  => 'visibility',
        ],
      ],
    ];

    CRM_Core_BAO_OptionGroup::ensureOptionGroupExists($optionGroupParam);
  }
  
  private function deleteActivityVisibilityOptionGroup() {
    $params = ['name' => 'activity_visibility'];
    CRM_Core_BAO_OptionGroup::retrieve($params, $defaults);

    if (!empty($defaults['id'])) {
      CRM_Core_BAO_OptionGroup::del($defaults['id']);
    }
  }

}
