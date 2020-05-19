<?php

use CRM_Calendar_ExtensionUtil as E;

class CRM_Calendar_Form_OverlayingCalendar extends CRM_Core_Form {

  /**
   * Array of enabled components
   *
   * @var array|bool
   */
  public $enabledComponents;

  function __construct($state = NULL, $action = CRM_Core_Action::NONE, $method = 'post', $name = NULL) {
    parent::__construct($state, $action, $method, $name);

    $this->enabledComponents = CRM_Calendar_Utils_Extension::getEnabledComponents();
  }

  /**
   * @throws \CRM_Core_Exception
   */
  public function preProcess() {
    if (!(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
      _calendar_civix_addJSCss();
    }

    $defaultView = CRM_Calendar_Settings::get(['default_view'])['default_view'];
    if ($defaultView == 'listMonth') {
      $defaultView = 'month';
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
    $this->assign('locale', CRM_Calendar_Utils_Locale::getCurrentLocaleForCalendar());
    $this->assign('default_view', $defaultView);
    $this->assign('timeFormat', CRM_Calendar_Settings::get(['time_format'])['time_format']);
    $this->assign('scrollTime', CRM_Calendar_Settings::get(['scroll_time'])['scroll_time']);
    $this->assign('imagePath', CRM_Calendar_Utils_Extension::getImagePath());

    CRM_Utils_System::setTitle(E::ts('Calendar'));
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {

    $this->addEntityRef('contact_id', E::ts('Contact'), [], FALSE);

    if (in_array('CiviEvent', $this->enabledComponents)) {
      $eventTypes = CRM_Event_PseudoConstant::eventType();
      $this->add('select', 'event_type', E::ts('Event type'), $eventTypes, FALSE, [
        'class' => 'crm-select2',
        'multiple' => 'multiple',
        'placeholder' => E::ts('- select -'),
      ]);
    }

    if (in_array('CiviCase', $this->enabledComponents)) {
      $caseTypes = CRM_Case_PseudoConstant::caseType();
      $this->add('select', 'case_type', E::ts('Case type'), $caseTypes, FALSE, [
        'class' => 'crm-select2',
        'multiple' => 'multiple',
        'placeholder' => E::ts('- select -'),
      ]);
    }

    $activityTypes = CRM_Calendar_Common_Activity::getTypes();
    $this->add('select', 'activity_type', E::ts('Activity Type'), $activityTypes, FALSE, [
      'class' => 'crm-select2',
      'multiple' => 'multiple',
      'placeholder' => E::ts('- select -'),
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
