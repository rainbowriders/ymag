app.directive('eventIcon', [function () {
    return {
        restrict: "E",
        scope: null,
        link: function (scope, element, attribs) {
            var event = JSON.parse(attribs.event);
            var icon = null;

            if (event.birthday) {
                icon = 'ti-gift';
            } else if (! event.allDay) {
                icon = 'ti-alarm-clock';
            }

            scope.icon = icon;
        },
        template: '<i ng-if="icon" class="{{ icon }}">&nbsp;</i>'
    };
}]);