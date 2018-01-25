app.controller('CalendarController', [
    '$scope', '$rootScope', 'EventsService', 'localStorageService',
    function ($scope, $rootScope, EventsService, localStorageService) {

        $scope.calendars = [];
        $scope.events = [];
        $scope.hasEvents = false;
        $scope.calendarEvents = [];
        $scope.multiDayEvents = [];
        var d = new Date();
        $scope.yesterday = new Date(d.setDate(d.getDate() - 1));
        $scope.init = function init (calendars) {

            $scope.calendars = calendars;
            setDefaultCalendar();

        };

        $scope.savePreferences = function savePreferences (cb) {

            var temp = [];

            for(var i = 0; i < $scope.calendars.length; i++) {
                if($scope.calendars[i].selected == true) {
                    temp.push($scope.calendars[i]);
                }
            }

            localStorageService.set(
                'cal',
                JSON.stringify(angular.copy(temp)));

            fetchEvents();

            if(cb) {
                cb();
            }
        };

        $scope.cancel = function cancel (callback) {
            resetCalendarsStatus();
            setDefaultCalendarsStatus(getSavedCalendars());
            if(callback) {
                callback();
            }
        };

        $scope.select = function (calendar) {
            checkUncheckCalendar(calendar);
        };

        $scope.$on('cardbox.close', function () {
            resetCalendarsStatus();
            setDefaultCalendarsStatus(getSavedCalendars());
        });

        $scope.$watchCollection('calendarEvents', function () {
            // $scope.events = [];
            // for( var i = 0; i < $scope.calendarEvents.length; i++) {
            //
            //     for (var a in $scope.calendarEvents[i]) {
            //         $scope.events.push($scope.calendarEvents[i][a]);
            //     }
            // }
            // if($scope.events.length > 0) {
            //     $scope.hasEvents = true;
            // }
            // $scope.events.sort(function (a, b) {
            //     a = new Date(a.date);
            //     b = new Date(b.date);
            //     return a < b ? -1 : a > b ? 1 : 0;
            // });
            var tempEvents = [];
            for(var i in $scope.calendarEvents) {
                var exist = false;
                for(var a in tempEvents) {
                    if(tempEvents[a].date.toString() == $scope.calendarEvents[i].date.toString()) {
                        exist = true;
                        for(var k in $scope.calendarEvents[i].events) {
                            var evtExist = false;
                            for(var z in tempEvents[a].events) {
                                if(tempEvents[a].events[z].id == $scope.calendarEvents[i].events[k].id) {
                                    evtExist = true;
                                    break;
                                }
                            }
                            if(evtExist == false) {
                                tempEvents[a].events.push($scope.calendarEvents[i].events[k]);
                            }
                        }
                        break;
                    }
                }
                if(exist == false) {
                    tempEvents.push($scope.calendarEvents[i]);
                }
            }
            // // console.log(tempEvents);
            if($scope.calendarEvents.length) {
                $scope.hasEvents = true;
            }
            //
            $scope.calendarEvents = tempEvents;
        });

        function setDefaultCalendar() {
            var saved = getSavedCalendars();

            if(!saved || saved == null) {
                $scope.calendars[0].selected = true;
                var temp = [];
                temp.push($scope.calendars[0]);
                localStorageService.set(
                    'cal',
                    JSON.stringify(angular.copy(temp)));
            } else {
                setDefaultCalendarsStatus(saved);
            }

            fetchEvents();
        }

        function checkUncheckCalendar(calendar) {

            for(var i =0; i < $scope.calendars.length; i++) {

                if($scope.calendars[i].id == calendar.id) {

                    // First time loaded
                    if(!$scope.calendars[i].selected || $scope.calendars[i].selected == 'undefined') {
                        $scope.calendars[i].selected = true;
                    } else {  // if user make changed
                        $scope.calendars[i].selected = !$scope.calendars[i].selected;
                    }
                }
            }
        }

        function getSavedCalendars() {
            return JSON.parse(localStorageService.get('cal'));
        }

        function setDefaultCalendarsStatus(calendars) {
            for(var i =0; i < calendars.length; i++) {
                setDefaultCalendarStatus(calendars[i]);
            }
        }

        function setDefaultCalendarStatus(calendar) {
            for( var i = 0; i < $scope.calendars.length; i++) {
                if(calendar.id == $scope.calendars[i].id) {
                    $scope.calendars[i].selected = calendar.selected || false;
                    break;
                }
            }
        }

        function resetCalendarsStatus() {
            for (var i = 0; i <$scope.calendars.length; i++) {
                $scope.calendars[i].selected = false;
            }
        }

        function fetchEvents () {
            $scope.calendarEvents = [];
            $scope.hasEvents = false;

            for (var i = 0; i < $scope.calendars.length; i++) {
                if($scope.calendars[i].selected == true) {
                    EventsService.events($scope.calendars[i].id)
                        .then(function (res) {
                            $scope.me = res.me;
                            $scope.multiDayEvents = [];
                            for(var c in res.events) {
                                var evt = transformDates(res.events[c]);
                                evt = removeDeclinedEvents(evt);
                                checkMultiDayEvent(evt);
                                // evt.events = onlyHourlyEvents(evt);
                                // if(evt.events.length > 0) {
                                //     $scope.calendarEvents.push(evt);
                                // }
                                $scope.calendarEvents.push(evt);
                            }
                            addMultiDayEvents();
                        });
                }
            }
        }

        function removeDeclinedEvents(evt) {
            for(var i in evt.events){
                if(removeEvent(evt.events[i].attendees)) {
                    evt.events.splice(i, 1);
                }
            }
            return evt;
        }

        function removeEvent(att) {
            for(var i in att) {
                if(att[i].email == $scope.me && att[i].responseStatus == 'declined') {
                    return true
                }
            }
            return false;
        }

        function onlyHourlyEvents(evt) {
            var result = [];
            for (var i in evt.events){
                if(evt.events[i].allDay) {
                    continue;
                } else {
                    result.push(evt.events[i]);
                }
            }

            return result;
        }

        function checkMultiDayEvent(evt) {
            for(var i in evt.events) {
                if(evt.events[i].allDay === true) {
                    var startDate = new Date(evt.events[i].start.date);
                    var endDate = new Date(new Date(evt.events[i].end.date));
                    if(startDate < endDate) {
                        $scope.multiDayEvents.push(evt.events[i]);
                    }

                }
            }
        }

        function transformDates(evt) {
            evt.date = new Date(evt.date);
            for(var i in evt.events) {
                var startDate = new Date(evt.events[i].start.date);
                var endDate = new Date(evt.events[i].end.date);
                endDate.setDate(endDate.getDate() - 1);
                evt.events[i].start.date = startDate;
                evt.events[i].end.date = endDate;

            }
            return evt;
        }

        function addMultiDayEvents() {
            for (var i in $scope.multiDayEvents) {
                var endDate = $scope.multiDayEvents[i].end.date;
                var startDate = $scope.multiDayEvents[i].start.date;
                addNewEventDay(startDate, endDate);

                pushEvent($scope.multiDayEvents[i]);
            }
        }


        function addNewEventDay(startDate, endDate) {
            var firstDay= new Date(startDate);
            while(endDate > firstDay) {
                var day = {
                    date: new Date(firstDay.setDate(firstDay.getDate() + 1)),
                    events: []
                };
                $scope.calendarEvents.push(day);
            }
        }

        function pushEvent(evt) {
            for(var i in $scope.calendarEvents) {
                var exist = false;
                for(var a in $scope.calendarEvents[i].events) {
                    if($scope.calendarEvents[i].events[a].id ==  evt.id) {
                        exist = true;
                        break;
                    }
                }
                var evtStartDate =  new Date(evt.start.date);
                var evtEndDate = new Date(evt.end.date);
                var calDate = new Date($scope.calendarEvents[i].date);

                if(exist == false) {
                    if(calDate >= evtStartDate && calDate <= evtEndDate) {
                        $scope.calendarEvents[i].events.push(evt);
                    }
                }
            }
        }

    }]);