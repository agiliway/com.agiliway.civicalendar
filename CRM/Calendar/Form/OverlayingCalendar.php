<?php

class CRM_Calendar_Form_OverlayingCalendar extends CRM_Core_Form {

  function __construct($state = NULL, $action = CRM_Core_Action::NONE, $method = 'post', $name = NULL) {
    parent::__construct($state, $action, $method, $name);

    $this->enabledComponents = CRM_Calendar_Common_Handler::getEnabledComponemnts();
  }

  /**
   * @throws \CRM_Core_Exception
   */
  public function preProcess() {
    $tsLocale = CRM_Core_I18n::getLocale();

    if (!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      _calendar_civix_addJSCss();
    }

    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this);
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
    $this->assign('imagePath', CRM_Calendar_Common_Handler::getImagePath());

    CRM_Utils_System::setTitle(ts('Calendar'));
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {

    $this->addEntityRef('contact_id', ts('Contact'), [], FALSE);

    if (in_array('CiviEvent', $this->enabledComponents)) {
      $eventTypes = CRM_Event_PseudoConstant::eventType();
      $this->add('select', 'event_type', ts('Event type'), $eventTypes, FALSE, [
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
    }

    $activityTypes = CRM_Calendar_Common_Activity::getTypes();
    $this->add('select', 'activity_type', ts('Activity Type'), $activityTypes, FALSE, [
      'class' => 'crm-select2',
      'multiple' => 'multiple',
      'placeholder' => ts('- select -'),
    ]);
  }

  /**
   * @return array|NULL
   * @throws \CRM_Core_Exception
   */
  public function setDefaultValues() {
    $defaults = [
      'contact_id' => CRM_Utils_Request::retrieve('cid', 'Integer', $this),
      'contact_ids' => [CRM_Utils_Request::retrieve('cid', 'Integer', $this)],
    ];

    return $defaults;
  }

}
