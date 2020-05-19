<?php

use CRM_Calendar_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Calendar_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    parent::buildQuickForm();

    CRM_Utils_System::setTitle(CRM_Calendar_Settings::TITLE . ' - ' . E::ts('Settings'));

    $settings = $this->getFormSettings();

    foreach ($settings as $name => $setting) {
      if (isset($setting['html_type'])) {
        switch ($setting['html_type']) {
          case 'Text':
            $this->addElement('text', $name, E::ts($setting['description']), $setting['html_attributes'], []);
            break;

          case 'Checkbox':
            $this->addElement('checkbox', $name, E::ts($setting['description']), '', '');
            break;

          case 'Select':
            $options = array();
            if (isset($setting['option_values'])) {
              $options = $setting['option_values'];
            } elseif (isset($setting['pseudoconstant'])) {
              $options = civicrm_api3('Setting', 'getoptions', array(
                'field' => 'civicalendar_'.$name,
              ));
              $options = $options['values'];
            }
            $select = $this->addElement('select', $name, E::ts($setting['description']), $options, $setting['html_attributes']);
            if (isset($setting['multiple'])) {
              $select->setMultiple($setting['multiple']);
            }
            break;
        }
      }
    }

    if (CRM_Calendar_Utils_CiviMobile::isCiviMobileApiEnable() && !CRM_Calendar_Utils_CiviMobile::isActivateCiviCalendarSettings()) {
      $message = 'CiviCRM calendar and CiviMobile calendar are not synchronized!';
      $message .= ' This may cause different info is shown on the calendar in CiviMobile app.';
      $message .= ' It is recommended to set “Synchronize with CiviCalendar” flag at <a href=';
      $message .= CRM_Utils_System::url('civicrm/civimobile/calendar/settings');
      $message .= '>CiviMobile Setting page</a>(need version higher than 5.0) to keep both calendars synchronized.';
      $this->assign('synchronizationNotice', E::ts($message));
    }

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ],
    ]);

    $this->assign('elementNames', $this->getRenderableElementNames());
  }

  function postProcess() {
    $changed = $this->_submitValues;
    $settings = $this->getFormSettings(TRUE);

    foreach ($settings as &$setting) {
      if ($setting['html_type'] == 'Checkbox') {
        $setting = FALSE;
      }
      else {
        $setting = NULL;
      }
    }

    $settingsToSave = array_merge($settings, array_intersect_key($changed, $settings));

    CRM_Calendar_Settings::save($settingsToSave);

    parent::postProcess();

    CRM_Core_Session::singleton()
      ->setStatus('Configuration Updated', CRM_Calendar_Settings::TITLE, 'success');
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    $elementNames = [];

    foreach ($this->_elements as $element) {
      $label = $element->getLabel();

      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }

    return $elementNames;
  }

  /**
   * Get the settings we are going to allow to be set on this form.
   *
   * @param bool $metadata
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  function getFormSettings($metadata = TRUE) {
    $nonPrefixedSettings = [];
    $settings = civicrm_api3('setting', 'getfields', ['filters' => CRM_Calendar_Settings::getFilter()]);

    if (!empty($settings['values'])) {
      foreach ($settings['values'] as $name => $values) {
        if ($metadata) {
          $nonPrefixedSettings[CRM_Calendar_Settings::getName($name, FALSE)] = $values;
        }
        else {
          $nonPrefixedSettings[CRM_Calendar_Settings::getName($name, FALSE)] = NULL;
        }
      }
    }

    $components = civicrm_api3('Setting', 'getvalue', [
      'name' => "enable_components",
    ]);
    if (!in_array('CiviCase', $components)) {
      unset($nonPrefixedSettings['case_types']);
    }
    if (!in_array('CiviEvent', $components)) {
      unset($nonPrefixedSettings['event_types']);
    }

    return $nonPrefixedSettings;
  }

  function setDefaultValues() {
    $settings = $this->getFormSettings(FALSE);
    $defaults = [];

    $existing = CRM_Calendar_Settings::get(array_keys($settings));

    if ($existing) {
      foreach ($existing as $name => $value) {
        $defaults[$name] = $value;
      }
    }

    return $defaults;
  }

}
