<div class="tabsBlockContent">
    <div class="add__ev__wrapper">
        <div class="add__ev__wrap">
            <div data-button="add-event" class="add-button add-button-active">
                <svg class="add-button-icon" viewBox="0 0 50 50">
                    <circle style="fill:#43B05C;" cx="25" cy="25" r="25"/>
                    <line style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="25" y1="13" x2="25" y2="38"/>
                    <line style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="37.5" y1="25" x2="12.5" y2="25"/>
                </svg>
                {ts}Add{/ts}
            </div>
            <div data-div="add-button-open" class="add-button-open" style="display: none">
                <a data-popup="1" href="/civicrm/case/add?reset=1&action=add&context=case&cid={$contactId}" >{ts}Cases{/ts}</a><br>
                <a data-popup="0" href="/civicrm/event/add?reset=1&action=add&context=participant&cid={$contactId}" >{ts}Events{/ts}</a><br>
                <a data-popup="1" href="/civicrm/activity?reset=1&action=add&context=standalone&cid={$contactId}" >{ts}Activities{/ts}</a>
            </div>
        </div>
    </div>
    <div class="filter__wrap description calendar_item_types">
        <label class="filter__item">
            <div class="filter__item-checkbox" style="border-color:#35D0AE">
                <div class="filter__item-check" style="border-color:#35D0AE"></div>
            </div>
            <input id="filterCheckboxCase" class="styled_checkbox cases_checkbox" type="checkbox" checked="checked" />
            <span>{ts}Cases{/ts}</span>
        </label>

        <label class="filter__item">
            <div class="filter__item-checkbox" style="border-color:#ff0000">
                <div class="filter__item-check" style="border-color:#ff0000"></div>
            </div>
            <input id="filterCheckboxEvents" class="styled_checkbox events_checkbox" type="checkbox" checked="checked" />
            <span>{ts}Events{/ts}</span>
        </label>

        <label class="filter__item">
            <div class="filter__item-checkbox" style="border-color:#F7CF5D">
                <div class="filter__item-check" style="border-color:#F7CF5D"></div>
            </div>
            <input id="filterCheckboxActivity" class="styled_checkbox activities_checkbox" type="checkbox" checked="checked" />
            <span>{ts}Activities{/ts}</span>
        </label>
    </div>
    <div id="contactEventCalendar"></div>
    <div
        data-div="settings"
        data-dayOfMonthFormat="{$settings.dayOfMonthFormat}"
        data-defaultView="{$settings.defaultView}"
        data-height="{$settings.height}"
        data-scrollTime="{$settings.scrollTime}"
        data-timeFormat="{$settings.timeFormat}"
        data-locale="{$settings.locale}"
    >
    </div>
</div>

{literal}
<script type="text/javascript">

    CRM.$(function ($) {
        $(document).ready(function () {
            initContactEventCalendar();
            initButtons();
        });

        function initContactEventCalendar() {
            var events_data;
            var checked_events = $('#filterCheckboxEvents').attr("checked");
            var checked_case = $('#filterCheckboxCase').attr("checked");
            var checked_activity = $('#filterCheckboxActivity').attr("checked");
            var settings = $('[data-div="settings"]');

            var events_calendar = CRM.$('#contactEventCalendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay,listMonth'
                },
                dayOfMonthFormat: settings.attr('data-dayOfMonthFormat'),
                height: settings.attr('data-height'),
                defaultView: settings.attr('data-defaultView'),
                scrollTime: settings.attr('data-scrollTime'),
                timeFormat: settings.attr('data-timeFormat'),
                locale: settings.attr('data-locale'),
                firstDay: 1,
                displayEventTime: true,
                eventSources: '',
                eventRender: eventRenderCallbackContactEventCalendar,
                eventClick: function (event, element) {
                    if (event.url) {
                        window.open(event.url);
                        return false;
                    }
                },
                dayClick: function (event, element) {},
                viewRender: function (view, element) {
                    var data = {
                        start: view.start.unix(),
                        end: view.end.unix()
                    };
                    if (typeof events_calendar !== 'undefined'){
                        events_calendar.fullCalendar('removeEvents');
                    }
                    $.ajax({
                        method: 'GET',
                        url: {/literal}'{crmURL p="civicrm/ajax/calendar" h=0 q="&cid=$contactId"}'{literal},
                        data: data,
                        dataType: 'json',
                        success: function (data) {
                            events_data = data;
                            if (typeof events_data['events'] !== 'undefined' && checked_events) {
                                events_calendar.fullCalendar('addEventSource', events_data['events']);
                            }
                            if (typeof events_data['case'] !== 'undefined'  && checked_case) {
                                events_calendar.fullCalendar('addEventSource', events_data['case']);
                            }
                            if (typeof events_data['activity'] !== 'undefined'  && checked_activity) {
                                events_calendar.fullCalendar('addEventSource', events_data['activity']);
                            }
                            events_calendar.fullCalendar('rerenderEvents');
                        }
                    });
                },
                eventDrop: function (event, element) {
                    return false;
                }
            });

            $('#filterCheckboxEvents').change(function() {
                if(this.checked) {
                    checked_events = true;
                    events_calendar.fullCalendar('addEventSource', events_data['events']);
                    return;
                }
                checked_events = false;
                events_calendar.fullCalendar( 'removeEventSource', events_data['events']);
            });

            $('#filterCheckboxCase').change(function() {
                if(this.checked) {
                    checked_case = true;
                    events_calendar.fullCalendar('addEventSource', events_data['case']);
                    return;
                }
                checked_case = false;
                events_calendar.fullCalendar( 'removeEventSource', events_data['case']);
            });

            $('#filterCheckboxActivity').change(function() {
                if(this.checked) {
                    checked_activity = true;
                    events_calendar.fullCalendar('addEventSource', events_data['activity']);
                    return;
                }
                checked_activity = false;
                events_calendar.fullCalendar('removeEventSource', events_data['activity']);
            });

        }

        function eventRenderCallbackContactEventCalendar(event, element) {
            var text = element.context.textContent;
            element.context.setAttribute("title", text);
        }

        function initButtons(){
            $(document).on('click', '[data-button="add-event"]', function() {
                var $addButtonOpen = $('[data-div="add-button-open"]');
    
                if ($addButtonOpen.css('display') === 'none') {
                    $addButtonOpen.css('left',($(this).width() + 10) + 'px');
                    $addButtonOpen.css('display', 'inline-block');
                }
                else {
                    $addButtonOpen.css('display', 'none');
                }
            });
    
            $(document).on('mouseup', 'body', function(e) {
                var container = $('[data-div="add-button-open"]');
                if (container.has(e.target).length === 0){
                    container.hide();
                }
            });
        }
        
        $(document).on('click', '[data-div="add-button-open"] a', function() {
           var 
            $el = $(this),url = $el.attr('href'),
            isPopup = $el.attr('data-popup');
          if (isPopup==1 && url) {
            CRM.loadForm(url).on('crmFormSuccess', function() {
              CRM.refreshParent($el);
              initButtons();
            });
            
          }else{
            window.open(url, "_blank");
          }
          return false;
        });
        
    });
</script>
{/literal}
