<?php

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Calendar_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    parent::buildQuickForm();

    CRM_Utils_System::setTitle(CRM_Calendar_Settings::TITLE . ' - ' . ts('Settings'));

    $settings = $this->getFormSettings();

    foreach ($settings as $name => $setting) {
      if (isset($setting['html_type'])) {
        switch ($setting['html_type']) {
          case 'Text':
            $this->addElement('text', $name, ts($setting['description']), $setting['html_attributes'], []);
            break;

          case 'Checkbox':
            $this->addElement('checkbox', $name, ts($setting['description']), '', '');
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
            $select = $this->addElement('select', $name, ts($setting['description']), $options, $setting['html_attributes']);
            if (isset($setting['multiple'])) {
              $select->setMultiple($setting['multiple']);
            }
            break;
        }
      }
    }

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
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
