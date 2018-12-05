<?php

class CRM_Calendar_Form_Calendar extends CRM_Core_Form {

  /**
   * Array of enabled components
   *
   * @var array|bool
   */
  public $enabledComponents;

  function __construct($state = NULL, $action = CRM_Core_Action::NONE, $method = 'post', $name = NULL) {
    parent::__construct($state, $action, $method, $name);

    $this->enabledComponents = CRM_Calendar_Common_Handler::getEnabledComponemnts();
  }

  /**
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public function preProcess() {
    if (!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      _calendar_civix_addJSCss();
    }

    $tsLocale = CRM_Core_I18n::getLocale();

    $settings = CRM_Calendar_Settings::get([
      'scrolltime',
      'defaultview',
      'dayofmonthformat',
      'timeformat',
      'hidepastevents',
      'locale',
      'height',
    ]);
    $settings['scrollTime'] = $settings['scrolltime'];
    $settings['defaultView'] = $settings['defaultview'];
    $settings['dayOfMonthFormat'] = $settings['dayofmonthformat'];
    $settings['timeFormat'] = $settings['timeformat'];
    $settings['hidePastEvents'] = $settings['hidepastevents'];
    $settings['locale'] = $settings['locale'];
    $settings['height'] = $settings['height'];

    $this->assign('settings', $settings);

    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);

    CRM_Core_Resources::singleton()
      ->addVars('agiliwaydashbord', ['cid' => $this->_contactId]);

    $this->assign('contactId', $this->_contactId);

    if (in_array('CiviEvent', $this->enabledComponents)) {
      $this->assign('eventColor', CRM_Calendar_Common_Event::EVENT_COLOR);
      $this->assign('event_is_enabled', TRUE);
    }

    if (in_array('CiviCase', $this->enabledComponents)) {
      $this->assign('caseColor', CRM_Calendar_Common_Case::CASE_COLOR);
      $this->assign('case_is_enabled', TRUE);
    }

    $this->assign('activityColor', CRM_Calendar_Common_Activity::ACTIVITY_COLOR);

    $this->assign('language', $tsLocale);

    CRM_Utils_System::setTitle(ts('Calendar'));
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {

    if (in_array('CiviEvent', $this->enabledComponents)) {
      $eventTypes = CRM_Event_PseudoConstant::eventType();
      $this->add('select', 'event_type', ts('Event type'), $eventTypes, FALSE, [
        'class' => 'crm-select2',
        'multiple' => 'multiple',
        'placeholder' => ts('- select -'),
      ]);

      $eventStatus = CRM_Calendar_Common_Event::getParticipantStatus();
      $this->add('select', 'event_participant_status', ts('Participant status'), $eventStatus, FALSE, [
        'class' => 'crm-select2',
        'multiple' => 'multiple',
        'placeholder' => ts('- select -'),
      ]);
    }

    if (in_array('CiviCase', $this->enabledComponents)) {
      $caseTypes = CRM_Case_PseudoConstant::caseType();
      $this->add('select', 'case_type', ts('Case type'), $caseTypes, FALSE, [
        'class' => 'crm-select2',
        'multiple' => 'multiple',
        'placeholder' => ts('- select -'),
      ]);

      $caseStatuses = CRM_Case_PseudoConstant::caseStatus();
      $this->add('select', 'case_status', ts('Case status'), $caseStatuses, FALSE, [
        'class' => 'crm-select2',
        'multiple' => 'multiple',
        'placeholder' => ts('- select -'),
      ]);
    }

    $activityTypes = CRM_Calendar_Common_Activity::getTypes();
    $this->add('select', 'activity_type', ts('Activity Type'), $activityTypes, FALSE, [
      'class' => 'crm-select2',
      'multiple' => 'multiple',
      'placeholder' => ts('- select -'),
    ]);

    $activityRoles = CRM_Calendar_Common_Activity::getRoles();
    $this->add('select', 'activity_role', ts('Activity Role'), $activityRoles, FALSE, [
      'class' => 'crm-select2',
      'placeholder' => ts('- select -'),
    ]);

    $activityStatus = CRM_Calendar_Common_Activity::getStatus();
    $this->add('select', 'activity_status', ts('Activity Status'), $activityStatus, FALSE, [
      'class' => 'crm-select2',
      'multiple' => 'multiple',
      'placeholder' => ts('- select -'),
    ]);
  }

}
