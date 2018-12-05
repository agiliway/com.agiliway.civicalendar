<div class="tabsBlockContent overlay-mode">
  <div class="row">
    <div class="col-md-9">
      <div class="contact-filter">

        <div class="crm-accordion-wrapper crm-search_filters-calendar-accordion collapsed">
          <div class="crm-accordion-header">
            {ts}Contact filter{/ts}
          </div><!-- /.crm-accordion-header -->
          <div class="crm-accordion-body">
            <table class="form-layout contact-filter-conteiner">
              <tr>
                <td style="vertical-align:top">
                  {$form.contact_id.html}
                  <div class="selected-contact-wrapper"></div>
                </td>
              </tr>
            </table>
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
          </table>

        </div><!-- /.crm-accordion-body -->
      </div><!-- /.crm-accordion-wrapper -->

      <div id="calendar" class="crm-summary-contactname-block crm-inline-edit-container"></div>
    </div>
  </div>

</div>

{literal}
<script type="text/javascript">
  CRM.$(function ($) {

    CRM.$(document).ready(function () {
      var $form = CRM.$('form.{/literal}{$form.formClass}{literal}');

      var contactIdSelect = CRM.$('#{/literal}{$form.contact_id.name}{literal}');

      {/literal}{if $event_is_enabled}{literal}
      var eventTypeSelect = CRM.$('#{/literal}{$form.event_type.name}{literal}');
      {/literal}{/if}{literal}

      {/literal}{if $case_is_enabled}{literal}
      var caseTypeSelect = CRM.$('#{/literal}{$form.case_type.name}{literal}');
      {/literal}{/if}{literal}

      var activityTypeSelect = CRM.$('#{/literal}{$form.activity_type.name}{literal}');
      var calendarSelects = CRM.$(
              {/literal}{if $event_is_enabled}{literal}
        '#{/literal}{$form.event_type.name}{literal}' + ',' +
              {/literal}{/if}{literal}

              {/literal}{if $case_is_enabled}{literal}
        '#{/literal}{$form.case_type.name}{literal}' + ',' +
              {/literal}{/if}{literal}

        '#{/literal}{$form.activity_type.name}{literal}'
      );
      var events_data;

      {/literal}{if $event_is_enabled}{literal}
      var filterCheckboxEvents = CRM.$('#filterCheckboxEvents');
      var checked_events = filterCheckboxEvents.prop('checked');
      {/literal}{/if}{literal}

      {/literal}{if $case_is_enabled}{literal}
      var filterCheckboxCase = CRM.$('#filterCheckboxCase');
      var checked_case = filterCheckboxCase.prop('checked');
      {/literal}{/if}{literal}

      var filterCheckboxActivity = CRM.$('#filterCheckboxActivity');
      var checked_activity = filterCheckboxActivity.prop('checked');

      if (!getCookie('selectedContactIds')) {
        setCookie('selectedContactIds', JSON.stringify([]));
      }

      var events_calendar = CRM.$('#calendar').fullCalendar({
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay'
        },
        dayOfMonthFormat: 'ddd DD',
        timeFormat: 'HH:mm',
        height: '500',
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
        defaultView: 'month',
        nowIndicator: true,
        displayEventTime: true,
        eventSources: '',
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
          setImgClass(eventObj.image_url, eventObj.contact_id);

          var piwStyle = ' style="width:32px;height:32px;margin-right:1px;"';
          var piStyle = ' style="width:32px;height:32px;"';
          var img = '' +
            '<div class="person-image-wrapper"' + piwStyle + '>' +
            '<div class="person-image"' + piStyle + '>' +
            '<img src="' + eventObj.image_url + '" alt="" class="circular" data-id="' + eventObj.contact_id + '" style="width:32px;height:32px;">' +
            '</div>' +
            '</div>' +
            '';

          $el.find('.fc-content').before(img);

          var tooltipHtml = renderTooltip(eventObj);

          $el.popover({
            title: eventObj.display_name,
            content: tooltipHtml,
            trigger: 'hover',
            placement: 'auto',
            container: 'body',
            html: true
          });
        }
      });

      calendarSelects.crmSelect2();
      calendarSelects.change(function () {
        renderEvents(events_calendar.fullCalendar('getView'));
      });

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

      contactIdSelect.change(function () {
        var contactIdVal = contactIdSelect.select2('val');
        var selectedContactIdsC = JSON.parse(getCookie('selectedContactIds'));
        var selectedContactIdsCSplited = spliting(selectedContactIdsC);

        if (selectedContactIdsCSplited.length >= 5) {
          CRM.alert('{/literal}{ts}You can only view 5 calendars at a time.{/ts}{literal}', ts('Error'), 'error');

          return;
        }

        if (CRM.$.inArray(contactIdVal + ':' + 1 || contactIdVal + ':' + 0, selectedContactIdsC) !== -1
          || CRM.$('.selected-contact-wrapper').find('input[type=checkbox][data-id=' + contactIdVal + ']').length > 0
        ) {
          return;
        }

        selectedContactIdsC.push(CRM.$(this).val() + ':' + 1);
        setCookie('selectedContactIds', JSON.stringify(selectedContactIdsC));
        renderSelectedContact();
        renderEvents(events_calendar.fullCalendar('getView'));

        CRM.$('#contact_id').val('');
        CRM.$('#contact_id').select2('val', '');
      });

      $form.on('click', '.selected-contact-wrapper .del', function () {
        var dataId = CRM.$(this).attr('data-id');

        CRM.$(this).parent('.selected-row').remove();

        var selectedContactIdsC = JSON.parse(getCookie('selectedContactIds'));
        selectedContactIdsC.splice(selectedContactIdsC.indexOf(dataId + ':' + 1 || dataId + ':' + 0), 1);
        setCookie('selectedContactIds', JSON.stringify(selectedContactIdsC));

        renderEvents(events_calendar.fullCalendar('getView'));
      });

      $form.on('change', '.selected-contact-wrapper input[type=checkbox]', function () {
        var dataId = CRM.$(this).attr('data-id');
        var selectedContactIdsC = JSON.parse(getCookie('selectedContactIds'));
        var selectedContactIdsCSplited = spliting(selectedContactIdsC);

        if (this.checked) {
          if (selectedContactIdsCSplited.length >= 5) {
            CRM.alert('{/literal}{ts}You can only view 5 calendars at a time.{/ts}{literal}', ts('Error'), 'error');
            CRM.$(this).prop('checked', false);

            return;
          }

          selectedContactIdsC[selectedContactIdsC.indexOf(dataId + ':' + '0')] = dataId + ':' + '1';
        }
        else {
          selectedContactIdsC[selectedContactIdsC.indexOf(dataId + ':' + '1')] = dataId + ':' + '0';
        }

        setCookie('selectedContactIds', JSON.stringify(selectedContactIdsC));
        renderEvents(events_calendar.fullCalendar('getView'));
      });

      CRM.$('.row-contact-field').find('#s2id_contact_id').addClass('action-link').find('a').addClass('button').html('<span>{/literal}{ts}Select Contact{/ts}{literal}</span>');

      renderSelectedContact();

      function renderEvents(view) {
        var data = getEventsData();
        data['start'] = view.start.unix();
        data['end'] = view.end.unix();

        if (typeof events_calendar !== 'undefined') {
          events_calendar.fullCalendar('removeEvents');
        }

        {/literal}
        var url = "{crmURL p="civicrm/ajax/calendar/overlaying" h=0}";
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

      function getEventsData() {
        {/literal}{if $event_is_enabled}{literal}
        var eventTypeId = eventTypeSelect.val();
        {/literal}{/if}{literal}

        {/literal}{if $case_is_enabled}{literal}
        var caseTypeId = caseTypeSelect.val();
        {/literal}{/if}{literal}

        var activityTypeId = activityTypeSelect.val();
        var selectedContactIdsC = spliting(JSON.parse(getCookie('selectedContactIds')));

        return {
          {/literal}{if $event_is_enabled}{literal}
          eventTypeId: eventTypeId,
          {/literal}{/if}{literal}
          {/literal}{if $case_is_enabled}{literal}
          caseTypeId: caseTypeId,
          {/literal}{/if}{literal}
          activityTypeId: activityTypeId,
          cid: selectedContactIdsC.length > 0 ? selectedContactIdsC : 0
        };
      }

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

      function renderSelectedContact() {
        var selectedContactIdsC = JSON.parse(getCookie('selectedContactIds'));
        var contactIds = [];
        var contactIdsChecked = [];

        for (var i = 0; i < selectedContactIdsC.length; i++) {
          var split = selectedContactIdsC[i].split(':');
          contactIds.push(split[0]);
          contactIdsChecked.push(split[1]);
        }

        CRM.api3('Contact', 'get', {
          'sequential': 1,
          'return': ['display_name', 'email', 'image_URL', 'contact_type'],
          'id': {'IN': contactIds}
        }).done(function (result) {
          var rows = '';

          if (result.is_error == 0) {
            CRM.$.each(result.values, function (index, value) {
              CRM.$.each(contactIds, function (i, v) {
                if (value.contact_id == v) {
                  value.is_checked = contactIdsChecked[i];
                }
              });

              rows += redrerRowSelectedContact(value);
            });

          }

          CRM.$('.selected-contact-wrapper').html(rows);
        });
      }

      function getAbbreviation(display_name) {
        if (display_name != null) {
          var displayNameItems = display_name.split(' ');

          if (displayNameItems.length > 0) {
            if (displayNameItems.length == 1) {
              var abbreviation = displayNameItems[0].substring(0, 1);
            } else if (displayNameItems.length == 2) {
              var abbreviation = displayNameItems[0].substring(0, 1) + displayNameItems[1].substring(0, 1);
            } else if (displayNameItems.length > 2) {
              var abbreviation = displayNameItems[0].substring(0, 1) + displayNameItems[1].substring(0, 1) + displayNameItems[2].substring(0, 1);
            }

            return abbreviation.toUpperCase();
          }
        }

        return '';
      }

      function redrerRowSelectedContact(value) {
        var isChecked = value.is_checked == 1 ? 'checked' : '';
        var imageURL = value.image_URL;

        if (!imageURL) {
          if (value.contact_type == 'Individual') {
            imageURL = '{/literal}{$imagePath}{literal}Person.svg';
          }
          else if (value.contact_type == 'Organization') {
            imageURL = '{/literal}{$imagePath}{literal}Organization.svg';
          }
          else {
            imageURL = '{/literal}{$imagePath}{literal}Person.svg';
          }
        }

        var contactHref = CRM.url('civicrm/contact/view', {reset: 1, cid: value.contact_id});
``
        setImgClass(imageURL, value.contact_id);

        var piwStyle = ' style="width:32px;height:32px;"';

        return '' +
          '<div class="selected-row">' +
          '<div class="checkbox-subrow">' +
          '<label for="show_contact-' + value.contact_id + '"><input ' +
          'id="show_contact-' + value.contact_id + '" ' +
          'name="show_contact-' + value.contact_id + '" ' +
          'type="checkbox" ' +
          'class="crm-form-checkbox" ' +
          'data-id="' + value.contact_id + '" ' +
          isChecked +
          '></label>' +
          '</div>' +

          '<div class="person-image" ' + piwStyle + '>' +
          '<img src="' + imageURL + '" alt="" class="circular" data-id="' + value.contact_id + '" style="width:32px;height:32px;">' +
          '</div>' +

          '<div class="contact-value">' +
          '<div>' +
          '<a href="' + contactHref + '" title="">' +
          value.display_name +
          '</a>' +
          '</div>' +
          '<div class="email-item">' +
          value.email +
          '</div>' +
          '</div>' +
          '<div class="del ui-icon ui-icon-close" data-id="' + value.contact_id + '">' +
          '</div>' +
          '</div>' +
          '';
      }

      function setImgClass(imageURL, dataId) {
        var tmpImg = new Image();
        tmpImg.src = imageURL;

        CRM.$(tmpImg).one('load', function () {
          var w = tmpImg.width;
          var h = tmpImg.height;

          CRM.$('.person-image img[data-id=' + dataId + ']').addClass(function () {
            if (h === w) {
              return 'square';
            } else if (h > w) {
              return 'portrait';
            } else {
              return 'landscape';
            }
          });
        });
      }

      function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = 'expires=' + d.toUTCString();
        document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
      }

      function getCookie(cname) {
        var name = cname + '=';
        var ca = document.cookie.split(';');

        for (var i = 0; i < ca.length; i++) {
          var c = ca[i];

          while (c.charAt(0) == ' ') {
            c = c.substring(1);
          }

          if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
          }
        }
        return '';
      }

      function spliting(selectedContactIdsC) {
        var returnArray = [];

        for (var i = 0; i < selectedContactIdsC.length; i++) {
          var split = selectedContactIdsC[i].split(':');
          if (split[1] == 1) {
            returnArray.push(split[0]);
          }
        }

        return returnArray;
      }
    });
  });
</script>
{/literal}
