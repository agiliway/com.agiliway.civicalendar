# CiviCalendar

The **CiviCalendar** extension (`com.agiliway.civicalendar`) is an CiviCRM component which allows visualizing different types of events and activities. The extension provides a rich intuitive user interface and simple navigation.

The Calendar allows:
* Viewing info on **different types** of activities (`Cases, Events, Activities`). Each type uses its own color
* Setup different **time periods** - month, week, day
* **Filtering** visualized data by types
* **Adding** new activity directly from the Calendar
* **Quickly previewing** the event info by one click

![Screenshot](/img/screenshot_m.png)

## Requirements

 * CiviCRM v4.6.x, v4.7.x

## Installation (git/cli)
 
To install the extension on an existing CiviCRM site:
```
mkdir sites/all/modules/civicrm/ext
cd sites/all/modules/civicrm/ext
git clone https://github.com/agiliway/com.agiliway.civicalendar civicalendar
cv en civicalendar
```
