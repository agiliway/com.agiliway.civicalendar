{* Contact Summary template for new tabbed interface. Replaces Basic.tpl *}
<div class="tabsBlockContent">

  <div class="add__ev__wrapper">
    <div class="add__ev__wrap">
      <div data-button="add-event" class="add-button add-button-active">
        <svg class="add-button-icon" viewBox="0 0 50 50">
          <circle style="fill:#43B05C;" cx="25" cy="25" r="25"/>
          <line style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="25" y1="13" x2="25" y2="38"/>
          <line style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="37.5" y1="25" x2="12.5" y2="25"/>
        </svg>
        {ts}Add{/ts}</div>
      <div data-div="add-button-open" class="add-button-open" style="display: none">
        {if $case_is_enabled}
          <a data-popup="0" href="{crmURL p='civicrm/case/add' q="reset=1&action=add&cid=`$contactId`&context=case" h=0}">{ts}Case{/ts}</a>{/if}
        {if $event_is_enabled}
          <a data-popup="0" href="{crmURL p='civicrm/contact/view/participant' q="reset=1&action=add&cid=`$contactId`&context=participant" h=0}">{ts}Event{/ts}</a>{/if}
        <a data-popup="0" href="{crmURL p='civicrm/activity' q="reset=1&action=add&context=standalone" h=0}">{ts}Activity{/ts}</a>
      </div>
    </div>
  </div>

  <div class="crm-accordion-wrapper crm-search_filters-calendar-accordion collapsed">
    <div class="crm-accordion-header">
      {ts}Edit Search Criteria{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
      <table class="form-layout">
        <tr>
          {if $event_is_enabled}
            <td>
              <label class="filter__item">
                <input id="filterCheckboxEvents" type="checkbox" checked="checked"
                       class="styled_checkbox events_checkbox"/>
                <span class="filter__item-checkbox" style="border-color:{$eventColor}">
                                <span class="filter__item-check" style="border-color:{$eventColor}"></span>
                            </span>
                <span>{ts}Event{/ts}</span>
              </label>
            </td>
          {/if}
          {if $case_is_enabled}
            <td>
              <label class="filter__item">
                <input id="filterCheckboxCase" type="checkbox" checked="checked"
                       class="styled_checkbox cases_checkbox"/>
                <span class="filter__item-checkbox" style="border-color:{$caseColor}">
                                <span class="filter__item-check" style="border-color:{$caseColor}"></span>
                            </span>
                <span>{ts}Case{/ts}</span>
              </label>
            </td>
          {/if}
          <td>
            <label class="filter__item">
              <input id="filterCheckboxActivity" type="checkbox" checked="checked"
                     class="styled_checkbox activities_checkbox"/>
              <span class="filter__item-checkbox" style="border-color:{$activityColor}">
                                <span class="filter__item-check" style="border-color:{$activityColor}"></span>
                            </span>
              <span>{ts}Activity{/ts}</span>
            </label>
          </td>
        </tr>
        <tr>
          {if $event_is_enabled}
            <td>
              {$form.event_type.label}
              {$form.event_type.html}
            </td>
          {/if}
          {if $case_is_enabled}
            <td>
              {$form.case_type.label}
              {$form.case_type.html}
            </td>
          {/if}
          <td>
            {$form.activity_type.label}
            {$form.activity_type.html}
          </td>
        </tr>
        <tr>
          {if $event_is_enabled}
            <td>
              {$form.event_participant_status.label}
              {$form.event_participant_status.html}
            </td>
          {/if}
          {if $case_is_enabled}
            <td>
              {$form.case_status.label}
              {$form.case_status.html}
            </td>
          {/if}
          <td>
            {$form.activity_status.label}
            {$form.activity_status.html}
          </td>
        </tr>
        <tr>
          {if $event_is_enabled}
            <td>
            </td>
          {/if}
          {if $case_is_enabled}
            <td>
            </td>
          {/if}
          <td>
            {$form.activity_role.label}
            {$form.activity_role.html}
          </td>
        </tr>
      </table>
    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->

  <div id="calendar" class="crm-summary-contactname-block crm-inline-edit-container"></div>
</div>

{literal}
<script type="text/javascript">
  function setEventAreasHeight() {
    var event_areas = CRM.$('.fc-time');

    CRM.$(event_areas).each(function () {
      CRM.$(this).parents('.fc-time-grid-event').height(CRM.$(this).height());
    });
  }

  CRM.$(function ($) {
    $(document).ready(function () {
      initFullCalendar();
      initButtons();
    });
  });

  CRM.$(document).ajaxSuccess(function (event, xhr, settings) {
    if ((!settings.dataType || settings.dataType == 'json') && xhr.responseText) {
      try {
        if (settings.url.indexOf('/civicrm/ajax/calendar') > -1) {
          setEventAreasHeight();
        }
      } catch (e) {
      }
    }
  });

  function initButtons() {
    CRM.$(document).on('click', '[data-button="add-event"]', function () {
      var $addButtonOpen = CRM.$('[data-div="add-button-open"]');

      if ($addButtonOpen.css('display') === 'none') {
        $addButtonOpen.css('left', (CRM.$(this).width() + 10) + 'px');
        $addButtonOpen.css('display', 'inline-block');
      }
      else {
        $addButtonOpen.css('display', 'none');
      }
    });

    CRM.$(document).on('mouseup', 'body', function (e) {
      var container = CRM.$('[data-div="add-button-open"]');

      if (container.has(e.target).length === 0) {
        container.hide();
      }
    });
  }

  function initFullCalendar() {
    {/literal}{if $event_is_enabled}{literal}
    var eventTypeSelect = CRM.$('#{/literal}{$form.event_type.name}{literal}');
    var eventParticipantStatusSelect = CRM.$('#{/literal}{$form.event_participant_status.name}{literal}');
    {/literal}{/if}{literal}

    {/literal}{if $case_is_enabled}{literal}
    var caseTypeSelect = CRM.$('#{/literal}{$form.case_type.name}{literal}');
    var caseStatusSelect = CRM.$('#{/literal}{$form.case_status.name}{literal}');
    {/literal}{/if}{literal}

    var activityTypeSelect = CRM.$('#{/literal}{$form.activity_type.name}{literal}');
    var activityStatusSelect = CRM.$('#{/literal}{$form.activity_status.name}{literal}');
    var activityRoleSelect = CRM.$('#{/literal}{$form.activity_role.name}{literal}');
    var calendarSelects = CRM.$(
            {/literal}{if $event_is_enabled}{literal}
      '#{/literal}{$form.event_type.name}{literal}' + ', #{/literal}{$form.event_participant_status.name}{literal}' + ',' +
            {/literal}{else}{literal}'' +{/literal}{/if}{literal}

            {/literal}{if $case_is_enabled}{literal}
      '#{/literal}{$form.case_type.name}{literal}' + ', #{/literal}{$form.case_status.name}{literal}' + ',' +
            {/literal}{else}{literal}'' +{/literal}{/if}{literal}

      '#{/literal}{$form.activity_type.name}{literal}' + ', #{/literal}{$form.activity_status.name}{literal}' + ', #{/literal}{$form.activity_role.name}{literal}'
    );

    calendarSelects.crmSelect2();
    calendarSelects.change(function () {
      renderEvents(events_calendar.fullCalendar('getView'));
    });

    var events_data;

    {/literal}{if $event_is_enabled}{literal}
    var filterCheckboxEvents = CRM.$('#filterCheckboxEvents');
    var checked_events = filterCheckboxEvents.prop("checked");
    {/literal}{/if}{literal}

    {/literal}{if $case_is_enabled}{literal}
    var filterCheckboxCase = CRM.$('#filterCheckboxCase');
    var checked_case = filterCheckboxCase.prop("checked");
    {/literal}{/if}{literal}

    var filterCheckboxActivity = CRM.$('#filterCheckboxActivity');
    var checked_activity = filterCheckboxActivity.prop("checked");
    var events_calendar = CRM.$('#calendar').fullCalendar({
      locale: '{/literal}{$settings.locale}{literal}',
      firstDay: 1,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listMonth'
      },
      dayOfMonthFormat: '{/literal}{$settings.dayOfMonthFormat}{literal}',
      timeFormat: '{/literal}{$settings.timeFormat}{literal}',
      scrollTime: '{/literal}{$settings.scrollTime}{literal}',
      minTime: '08:00:00',
      maxTime: '20:00:00',
      eventLimit: 3,
      businessHours: {
        start: '09:00',
        end: '19:00',
        dow: [1, 2, 3, 4, 5]
      },
      eventLimitText: "",
      lang: '{/literal}{$language}{literal}',
      defaultView: '{/literal}{$settings.defaultView}{literal}',
      nowIndicator: true,
      displayEventTime: true,
      eventSources: '',
      height: '{/literal}{$settings.height}{literal}',
      contentHeight: ({/literal}{$settings.height}{literal}+40),
      eventClick: function (event, element) {
      },
      dayClick: function (event, element) {
      },
      eventMouseover: function (calEvent, jsEvent) {
      },
      eventMouseout: function (calEvent, jsEvent) {
      },
      viewRender: function (view, element) {
        renderEvents(view);
      },
      eventDrop: function (event, element) {
        return false;
      },
      eventRender: function (eventObj, $el) {
        var tooltipHtml = renderTooltip(eventObj);
        $el.popover({
          content: tooltipHtml,
          trigger: 'hover',
          placement: 'auto',
          container: 'body',
          html: true
        });
      }
    });

    function renderEvents(view) {
      var data = getEventsData();
      data['start'] = view.start.unix();
      data['end'] = view.end.unix();

      if (typeof events_calendar !== 'undefined') {
        events_calendar.fullCalendar('removeEvents');
      }

      {/literal}
      var url = "{crmURL p="civicrm/ajax/calendar" q="cid=`$contactId`" h=0}";
      {literal}

      CRM.$.ajax({
        method: 'GET',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
          events_data = data;

          {/literal}{if $event_is_enabled}{literal}
          if (typeof events_data['events'] !== 'undefined' && checked_events) {
            events_calendar.fullCalendar('addEventSource', events_data['events']);
          }
          {/literal}{/if}{literal}

          {/literal}{if $case_is_enabled}{literal}
          if (typeof events_data['case'] !== 'undefined' && checked_case) {
            events_calendar.fullCalendar('addEventSource', events_data['case']);
          }
          {/literal}{/if}{literal}

          if (typeof events_data['activity'] !== 'undefined' && checked_activity) {
            events_calendar.fullCalendar('addEventSource', events_data['activity']);
          }

          events_calendar.fullCalendar('rerenderEvents');
        }
      });
    }

    {/literal}{if $event_is_enabled}{literal}
    filterCheckboxEvents.change(function () {
      if (this.checked) {
        checked_events = true;
        events_calendar.fullCalendar('addEventSource', events_data['events']);

        return;
      }

      checked_events = false;
      events_calendar.fullCalendar('removeEventSource', events_data['events']);
    });
    {/literal}{/if}{literal}

    {/literal}{if $case_is_enabled}{literal}
    filterCheckboxCase.change(function () {
      if (this.checked) {
        checked_case = true;
        events_calendar.fullCalendar('addEventSource', events_data['case']);

        return;
      }

      checked_case = false;
      events_calendar.fullCalendar('removeEventSource', events_data['case']);
    });
    {/literal}{/if}{literal}

    filterCheckboxActivity.change(function () {
      if (this.checked) {
        checked_activity = true;
        events_calendar.fullCalendar('addEventSource', events_data['activity']);

        return;
      }

      checked_activity = false;
      events_calendar.fullCalendar('removeEventSource', events_data['activity']);
    });

    function getEventsData() {
      {/literal}{if $event_is_enabled}{literal}
      var eventTypeId = eventTypeSelect.val();
      var eventParticipantStatusId = eventParticipantStatusSelect.val();
      {/literal}{/if}{literal}

      {/literal}{if $case_is_enabled}{literal}
      var caseTypeId = caseTypeSelect.val();
      var caseStatusId = caseStatusSelect.val();
      {/literal}{/if}{literal}

      var activityTypeId = activityTypeSelect.val();
      var activityStatusId = activityStatusSelect.val();
      var activityRoleId = activityRoleSelect.val();

      return {
        {/literal}{if $event_is_enabled}{literal}
        eventTypeId: eventTypeId,
        eventStatusId: eventParticipantStatusId,
        {/literal}{/if}{literal}
        {/literal}{if $case_is_enabled}{literal}
        caseTypeId: caseTypeId,
        caseStatusId: caseStatusId,
        {/literal}{/if}{literal}
        activityTypeId: activityTypeId,
        activityStatusId: activityStatusId,
        activityRoleId: activityRoleId
      };
    }

  }

  // calendar drop down
  CRM.$('.add-batton').click(function () {
    var addBtnOpen = CRM.$('.add-batton-open');
    var addBtn = CRM.$('.add-batton');

    if (addBtnOpen.css('display') === 'none') {
      addBtnOpen.css('display', 'block');
      addBtn.addClass('add-batton-active');
    } else {
      addBtnOpen.css('display', 'none');
      addBtn.removeClass('add-batton-active');
    }
  });

  function renderTooltip(event) {
    var tooltip = '<div class="tooltipEvent" >';

    tooltip += renderRowTooltip(event.title, '{/literal}{ts}Subject{/ts}{literal}');
    tooltip += renderRowTooltip(event.activityType, '{/literal}{ts}Type{/ts}{literal}');
    tooltip += renderRowTooltip(event.priority, '{/literal}{ts}Priority{/ts}{literal}');
    tooltip += renderRowTooltip(event.participantRole, '{/literal}{ts}Participant Role{/ts}{literal}');
    tooltip += renderRowTooltip(event.eventType, '{/literal}{ts}Type{/ts}{literal}');

    tooltip += '</div>';

    return tooltip;
  }

  function renderRowTooltip(value, title) {
    if (typeof value != 'undefined' && value != '') {
      var tooltipContent = '<div><b>' + title + '</b>: ' + value + '</div>';
      return tooltipContent;
    }

    return '';
  }
</script>

{/literal}
