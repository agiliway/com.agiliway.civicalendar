$.fullCalendar.locale('de', {
    buttonText: {
        today: 'heute',
        month: 'monat',
        week: 'woche',
        day: 'tag',
        list: 'liste'
    },
    allDayText: 'Aller tag',
    eventLimitText: function(n) {
        return '+mehr ' + n + '...';
    },
    noEventsMessage: 'Es gibt keine events zu zeigen',
    monthNames: [
        'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
        'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
    ],
    monthNamesShort: [
        'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun',
        'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'
    ],
    dayNames: [
        'Sonntag', 'Montag', 'Dienstag', 'Mittwoch',
        'Donnerstag', 'Fritag', 'Samstag'
    ],
    dayNamesShort: [
        'Son', 'Mon', 'Die', 'Mit', 'Don', 'Fre', 'Sam'
    ],
    weekNumberTitle: 'W',
    eventLimitText: 'mehr'
});
