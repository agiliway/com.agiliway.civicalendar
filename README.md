# CiviCalendar

The **CiviCalendar** extension (`com.agiliway.civicalendar`) is an CiviCRM component which allows visualizing the planned cases, events and activities. The extension provides a rich intuitive user interface and many complementary features.

The Calendar allows:
* Viewing info on **different types** of activities (`Cases, Events, Activities`)
* Easily **distinguishing** between the types of plans by the color of the line that appears before the title
* Viewing info for different **time periods** - month, week, day
* **Filtering** visualized data by 7 different parameters: event type, participant's status on the event, case type, case status, activity type, activity status and role in an activity
* Displaying or hiding **past events**
* Displaying the **plans of 5 other constituents** in one’s own graphical calendar
* **Adding** new activity directly from the Calendar
* **Quickly previewing** the event info in one click​
* Display Calendar on CiviCRM dashboard as **​dashlet**
* Includes API for CiviCRM and CiviMobileApp integration
* Supports 73 ​**locales**
* **Quick viewing** event participants and registering new ones directly from previewing form
*  Making activity types either hidden or visible 

## Screenshots

![Screenshot](/img/calendar_dashlet.png)
---
![Screenshot](/img/calendar_dashlet_list.png)
---
![Screenshot](/img/calendar_view.png)
---
![Screenshot](/img/calendar_sharing.png)
---
![Screenshot](/img/calendar_register_participant.png)
---
![Screenshot](/img/calendar_hide_activity.png)


## Requirements

 * CiviCRM v4.6.x, v4.7.x, v5.x

## Installation (git/cli)
 
To install the extension on an existing CiviCRM site:
```
mkdir sites/all/modules/civicrm/ext
cd sites/all/modules/civicrm/ext
git clone https://github.com/agiliway/com.agiliway.civicalendar com.agiliway.civicalendar
```
