/**
 * @param {Scope} scope
 * @param {Function} callback
 */
angular.safeApply = function (scope, callback) {
    scope[(scope.$$phase || scope.$root.$$phase) ? '$eval' : '$apply'](callback || function() {});
};

angular.isMobile = (function(a)
{
    return /((iP([oa]+d|(hone)))|Android|WebOS|BlackBerry|windows (ce|phone))/i.test(a);
})(navigator.userAgent||navigator.vendor||window.opera);

angular.isOnline = function isOnline()
{
    var isOnline = (window.navigator && window.navigator.onLine);

    return isOnline;
};

angular.storagePrefix = function (path) {
    var namespace = [
        'ymag', window['gid']
    ];

    if (path && path.length) {
        namespace.push(path);
    }

    return namespace.join('.');
};
var app = angular.module('app', ['ngSanitize', 'LocalStorageModule', 'countdownTimer']);

app.config(['localStorageServiceProvider', '$httpProvider', function (localStorageServiceProvider, $httpProvider) {
    var namespace = angular.storagePrefix();
    $httpProvider.defaults.headers.common.Authorization = 'Bearer ' + window['api_token'];
    localStorageServiceProvider.setPrefix(namespace);
    localStorageServiceProvider.setStorageCookie(1, '/');
}]);

app.run(['$rootScope', function ($rootScope) {
    window.onclick = function (event) {
        if (0 == $(event.target).closest('div.card-actions.dropdown.open').length
            && 0 == $(event.target).closest('#cities-list').length) {
            $rootScope.$broadcast('cardbox.close');
        }
    };
}]);

app.REWRITE_BASE = '/';
if (location.host == 'dev-your-morning.rainbowriders.dk') {
    app.REWRITE_BASE = '/public/';
}

app.API_PREFIX = app.REWRITE_BASE + 'api/v1';

app.controller('CalendarController', [
    '$scope', '$rootScope', 'EventsService', 'localStorageService',
    function ($scope, $rootScope, EventsService, localStorageService) {

        $scope.calendars = [];
        $scope.events = [];
        $scope.hasEvents = false;
        $scope.calendarEvents = [];
        
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
            $scope.events = [];
            for( var i = 0; i < $scope.calendarEvents.length; i++) {

                for (var a in $scope.calendarEvents[i]) {
                    $scope.events.push($scope.calendarEvents[i][a]);
                }
            }
            if($scope.events.length > 0) {
                $scope.hasEvents = true;
            }
            $scope.events.sort(function (a, b) {
                a = new Date(a.date);
                b = new Date(b.date);
                return a < b ? -1 : a > b ? 1 : 0;
            });
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
                            $scope.calendarEvents.push(res);
                        });
                }
            }
        }
    }]);
app.controller('CustomEventController', ['$scope', '$rootScope', '$interval', 'localStorageService', 'CustomEventService',
function ($scope, $rootScope, $interval, localStorageService, CustomEventService) {


    $scope.eventTitle = '';

    $scope.options = {
        selectedTime: 1
    };
    $rootScope.eventError = {};
    $scope.event = {};
    $scope.timeNow = new Date();
    $scope.loading = true;
    var weekInMilSeconds = 1000 * 60 * 60 * 24 * 7;
    var dayInMilSeconds = 1000 * 60 * 60 * 24;
    var hourInMilSeconds = 1000 * 60 * 60;
    var minuteInMilSeconds = 1000 * 60;
    var secondsInMilSeconds = 1000;


    $scope.setSelectedValue = function (val) {
        return $scope.options.selectedTime = parseInt(val);
    };

    $scope.createEvent = function (callback, title) {

        $rootScope.eventError = {};
        var date = document.getElementById("datepicker-autoclose").value;
        if($scope.options.selectedTime != 3) {
            var hours = document.getElementById("custom-event-hours").value;
            var minutes = document.getElementById("custom-event-minutes").value;
            var seconds = document.getElementById("custom-event-seconds").value;
        }

        if(date == '' || date == 'undefined') {
            $rootScope.eventError.eventDate = 'Please set a event date!';
        }
        if(title == '' || title == 'undefined') {
            $rootScope.eventError.eventTitle = 'Please set a event title!';
        }

        if($scope.options.selectedTime == 3) {
            hours = 23;
            minutes = 59;
            seconds = 59;
        }

        var dateToArr = transformDate(date);

        var tempDate = new Date(dateToArr[2] + '-' + dateToArr[1] + '-' + dateToArr[0]);
        if(hours) {
            tempDate.setHours(parseInt(hours));
        } else {
            tempDate.setHours(0);
        }
        if(minutes) {
            tempDate.setMinutes(parseInt(minutes));
        } else {
            tempDate.setMinutes(0);
        }
        if(seconds) {
            tempDate.setSeconds(parseInt(seconds));
        } else {
            tempDate.setSeconds(0);
        }

        if(tempDate < new Date()) {
            $rootScope.eventError.invalidTime = 'Event time must be in the future';
        }

        if(Object.keys($rootScope.eventError).length > 0) {
            return;
        }

        var data = {
            date: date,
            minutes: minutes || null,
            hours: hours || null,
            seconds: seconds || null,
            title: title,
            time_option: $scope.options.selectedTime
        };

        if($scope.event != null) {

            data.id = $scope.event.id;
            CustomEventService.updateEvent(data)
                .then(function (res) {
                    fetchEvent();
                    if(callback) {
                        callback();
                    }
                });
        } else {
            CustomEventService.createEvent(data)
                .then(function (res) {
                    fetchEvent();
                    if(callback) {
                        callback();
                    }
                });
        }
    };

    $scope.cancel = function (callback) {
        if(callback) {
            callback();
        }
    };

    function transformDate(arg) {
        return arg.split('.');
    }

    function fetchEvent() {
        $scope.loading = true;
        CustomEventService.getEvent()
            .then(function (res) {
                handleEvent(res);
                watchClockInterval();
            })
    }
    function parseDateTimeForIE(str) {

        var dateAndTimeArr = str.split(' ');
        var dateToArr = dateAndTimeArr[0].split('-');
        var timeToArr = dateAndTimeArr[1].split(':');
        
        return new Date(dateToArr[0], dateToArr[1] - 1, dateToArr[2], timeToArr[0], timeToArr[1], timeToArr[2]);
    }

    function handleEvent(res) {
        if(res == 'No event created yet') {
            $scope.event = null;
            return;
        } else if(new Date(res.time) < new Date() ) {
            $scope.event = null;
            return;
        }
        $scope.event = res;
        $scope.event.time = parseDateTimeForIE($scope.event.time);
        $scope.options.selectedTime = parseInt(res.time_option);
        $scope.loading = false;
        $scope.eventTimeToString = eventTimeToString($scope.event.time);
        $scope.eventDateToString = eventDateToString($scope.event.time);
        calculateTime($scope.event.time, $scope.options.selectedTime);
    }

    var watchClockInterval = function () {
        $interval(function () {
            if($scope.event == null) {
                $interval.cancel(watchClockInterval);
                return;
            }
            if (new Date($scope.event.time) < new Date()) {
                return $scope.event = null;
            }
            calculateTime($scope.event.time, $scope.options.selectedTime);
        }, 1000);
    };

    function calculateTime(time, timeOption) {

        var timeToEvent = time - new Date();
        switch (timeOption) {
            case 1:
                var weeks = getWeeksAndRest(timeToEvent);
                var days = getDaysAndRest(weeks.rest);
                var hours = getHoursAndRest(days.rest);
                var minutes = getMinutesAndRest(hours.rest);
                var seconds = getSeconds(minutes.rest);
                generateTimeStringOutput(weeks.weeks, days.days, hours.hours, minutes.minutes, seconds.seconds, timeOption);
                break;
            case 2:
                var days = getDaysAndRest(timeToEvent);
                var hours = getHoursAndRest(days.rest);
                minutes = getMinutesAndRest(hours.rest);
                var seconds = getSeconds(minutes.rest);
                generateTimeStringOutput(null, days.days, hours.hours, minutes.minutes, seconds.seconds, timeOption);
                break;
            case  3:
                var days = getDaysAndRest(timeToEvent);
                generateTimeStringOutput(null, days.days, null, null, null, timeOption);
                break;
            default:
                break;
        }
    }

    $scope.$watch('options.selectedTime', function () {
        if($scope.loading == true) {
            return;
        }
        calculateTime($scope.event.time, $scope.options.selectedTime);
    });

    function getWeeksAndRest(time) {
        return {
            weeks: parseInt(new Date(time).getTime() / weekInMilSeconds),
            rest: new Date(time).getTime() % weekInMilSeconds
        }
    }

    function getDaysAndRest(time) {
        return {
            days: parseInt(new Date(time).getTime() / dayInMilSeconds),
            rest: new Date(time).getTime() % dayInMilSeconds
        }
    }

    function getHoursAndRest(time) {
        return {
            hours: parseInt(new Date(time).getTime() / hourInMilSeconds),
            rest: new Date(time).getTime() % hourInMilSeconds
        }
    }

    function getMinutesAndRest(time) {
        return {
            minutes: parseInt(new Date(time).getTime() / minuteInMilSeconds),
            rest: new Date(time).getTime() % minuteInMilSeconds
        }
    }

    function getSeconds(time) {
        return {
            seconds: parseInt(new Date(time).getTime() / secondsInMilSeconds)
        }
    }

    function generateTimeStringOutput(weeks, days, hours, minutes, seconds, timeOption) {
        switch (timeOption) {
            case 1:
                var weeksStr = weeks == 1 ? weeks + ' week ' : weeks + ' weeks ';
                var daysStr = days == 1 ? days + ' day ' : days + ' days ';
                var hoursStr = hours == 1 ? hours + ' hour ' : hours + ' hours ';
                var minutesStr = minutes == 1 ? minutes + ' minute ' : minutes + ' minutes ';
                var secondsStr = seconds == 1 ? seconds + ' second ' : seconds + ' seconds ';
                $scope.timeLeftToString = 'In ' + weeksStr + daysStr + hoursStr + minutesStr + secondsStr;
                break;
            case 2:
                var daysStr = days == 1 ? days + ' day ' : days + ' days ';
                var hoursStr = hours == 1 ? hours + ' hour ' : hours + ' hours ';
                var minutesStr = minutes == 1 ? minutes + ' minute ' : minutes + ' minutes ';
                var secondsStr = seconds == 1 ? seconds + ' second ' : seconds + ' seconds ';
                $scope.timeLeftToString = 'In ' + daysStr + hoursStr + minutesStr + secondsStr;
                break;
            case 3:
                var daysStr = days == 1 ? days + ' day ' : days + ' days ';
                $scope.timeLeftToString = 'In ' + daysStr;
                break;
            default:
                break;
        }
    }
    function eventTimeToString(time) {
        var year = time.getFullYear();
        var date = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        var month = time.getMonth() < 10 + 1? '0' + (time.getMonth() +1): time.getMonth() + 1;
        var hour = time.getHours() < 10 ? '0' + time.getHours(): time.getHours();
        var minutes = time.getMinutes() < 10 ? '0' + time.getMinutes() : time.getMinutes();
        var output = date + '.' + month + '.' + year + ', ' + hour + ':' + minutes;

        return output;
    }

    function eventDateToString(time) {
        var year = time.getFullYear();
        var date = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        var month = time.getMonth() < 10 + 1? '0' + (time.getMonth() +1): time.getMonth() + 1;
        var output = date + '.' + month + '.' + year;

        return output;
    }
    fetchEvent();
}]);
app.controller('GmailController', ['$scope', 'GmailService', '$sce', 'localStorageService',
    function ($scope, GmailService, $sce, localStorageService) {
        $scope.searchMode = true;

        $scope.message = null;

        $scope.loading = false;

        $scope.nextPageToken = null;

        var emptyFilter = function () {
            return {
                'from': '',
                'to': '',
                'subject': '',
                'includeSpamTrash': false
            };
        };

        var savedFilter;
        if (!(savedFilter = localStorageService.get('g_fltr'))) {
            savedFilter = JSON.stringify(emptyFilter());
            localStorageService.set('g_fltr', savedFilter);
        }

        $scope.filter = JSON.parse(savedFilter);

        $scope.messages = [];

        $scope.query = buildQuery();

        function buildQuery() {
            $scope.query = '';

            var q = [];
            angular.forEach(['from', 'to', 'subject'], function (field, index, values) {
                var value = $scope.filter[field];

                if (value.length) {
                    q.push(field + ': (' + value + ')');
                }
            });

            $scope.query = q.join(" ").trim();

            return $scope.query;
        }

        $scope.savePreferences = function (cb) {
            $scope.messages = [];
            $scope.nextPageToken = null;

            return $scope.fetchMessages(cb);
        };

        $scope.next = $scope.fetchMessages = function (cb) {
            if ($scope.loading) return false;

            $scope.loading = true;

            // save filter
            localStorageService.set('g_fltr', savedFilter = JSON.stringify($scope.filter));

            var args = {
                'includeSpamTrash': !!$scope.filter.includeSpamTrash,
                'q': buildQuery(),
                'nextPageToken': $scope.nextPageToken
            };

            return GmailService.fetchMessages(args)
                .then(function (messages) {
                    if (cb) {
                        cb();
                    }
                    // restore listing view
                    angular.safeApply($scope, function ($scope) {
                        for (var i in messages.messages) {
                            if(isFromInbox(messages.messages[i])) {
                                $scope.messages.push(messages.messages[i]);
                            }
                        }
                        $scope.nextPageToken = messages.nextPage;

                        $scope.loading = false;
                    });
                })
                .catch(function () {
                    $scope.loading = false;
                });
        };

        // fetch messages on page ready
        $scope.fetchMessages();

        $scope.isUnRead = function (message) {
            return message.hasOwnProperty('labels')
                && (-1 < message.labels.indexOf('UNREAD'));
        };

        $scope.fullMessageUrl = function (messageId) {
            return $sce.trustAsResourceUrl(app.API_PREFIX + '/gmail/messages/' + messageId + '/body');
        };

        $scope.toggleSearchMode = function (flag, callback) {
            if (!flag) {
                $scope.filter = JSON.parse(savedFilter);
            }

            // $scope.searchMode = !!flag;

            if (callback) {
                callback();
            }
        };

        $scope.backToList = function () {
            $scope.message = null;
        };

        $scope.readMessage = function (messageId) {
            $scope.loading = true;

            GmailService.get(messageId)
                .then(function (message) {
                    angular.safeApply($scope, function ($scope) {
                        $scope.message = message;

                        $scope.loading = false;

                        var currentMessage = $scope.messages.filter(function (message) {
                            return message.id == messageId;
                        })[0];

                        $scope.messages.map(function (message) {
                            if (message.id == messageId && $scope.isUnRead(message)) {
                                var index = message.labels.indexOf('UNREAD');

                                message.labels.splice(index, 1);

                                GmailService.markAsRead(messageId);
                            }

                            return message;
                        });
                    });
                })
                .catch(function () {
                    $scope.loading = false;
                });
        };

        function isFromInbox (msg) {
            for (var i in msg.labels) {
                if(msg.labels[i] == 'INBOX') {
                    return true;
                }
            }
            return false;
        }

    }]);
app.controller('QuoteController', ['$scope', '$http', function ($scope, $http) {
    $scope.quote = {
        id: null,
        quote: '',
        author: ''
    };

    $scope.loading = false;

    /**
     * Fetch random quote
     */
    $scope.fetchRandom = function () {
        if ($scope.loading) return false;

        $scope.loading = true;
        $http.get(app.API_PREFIX + '/quotes/random').then(function (response) {
            $scope.quote = response.data;
            $scope.loading = false;
        });
    }
}]);
app.controller('RssController', [
    '$scope', '$timeout', '$rootScope', 'localStorageService', 'FeedService', '$q',
    function ($scope, $timeout, $rootScope, localStorageService, FeedService, $q) {
        $scope.loading = false;


        function key(path) {
            return window['lang'] + '.' + path;
        }

        function fullList() {
            return mapToInt(_.pluck($scope.allFeeds, 'id'));
        }

        function mapToInt(values) {
            return values.map(function (value) {
                return parseInt(value);
            });
        }

        function restoreReadableFeeds() {
            var savedFeeds;
            var hasSavedFeeds = localStorageService.keys().indexOf(key('feeds')) > -1;

            $feeds = [];

            if (!hasSavedFeeds) {
                $feeds = fullList();
            } else {
                savedFeeds = localStorageService.get(key('feeds'));
                if (savedFeeds.length) {
                    $feeds = mapToInt(savedFeeds.split(','));
                }
            }

            $scope.savedFeeds = angular.copy($feeds);

            allChecked();
        }

        function parseDateTimeForIE(str) {

            var dateAndTimeArr = str.split(' ');
            var dateToArr = dateAndTimeArr[0].split('-');
            var timeToArr = dateAndTimeArr[1].split(':');

            return new Date(dateToArr[0], dateToArr[1] - 1, dateToArr[2], timeToArr[0], timeToArr[1], timeToArr[2]);
        }
        
        function fetchNews() {
            var defer = $q.defer();

            if ($scope.loading || !$feeds.length) {
                defer.resolve([]);
            } else {
                $scope.loading = true;

                FeedService.news($feeds).then(function (news) {
                    $scope.loading = false;
                    $scope.articles = news;

                    for (var i in $scope.articles){
                        $scope.articles[i].pubDate.date = parseDateTimeForIE($scope.articles[i].pubDate.date);
                    }
                    defer.resolve(news);
                });
            }
            return defer.promise;
        }

        $scope.allChecked = false;

        // all feeds
        $scope.allFeeds = [];

        // readable feeds
        var $feeds = [];

        $scope.savedFeeds = [];

        $scope.articles = [];


        function allChecked() {
            $scope.allChecked = ($feeds.length == $scope.allFeeds.length);
        }

        $scope.$watch('feeds', function (v1, v2) {
            if (v1 === v2) return false;

            allChecked();
        }, true);

        $scope.toggleAll = function ($event) {
            if ($event.target.checked == true) {
                $feeds = fullList();
            } else {
                $feeds = [];
            }
        };

        $scope.init = function (allFeeds) {
            $scope.allFeeds = allFeeds;
            restoreReadableFeeds();

            fetchNews();
        };

        $scope.savePreferences = function (cb) {
            $scope.savedFeeds = mapToInt($feeds);

            localStorageService.set(key('feeds'), $scope.savedFeeds.join(','));

            return fetchNews().then(function () {
                if (cb) {
                    cb();
                }
            });
        };

        $scope.cancel = function (cb) {
            restoreReadableFeeds();

            if (cb) {
                cb();
            }
        };

        $scope.trackUntrack = function (feed_id) {
            feed_id = parseInt(feed_id);

            if ($scope.trackable(feed_id)) {
                $feeds = _.without($feeds, feed_id);
            } else {
                $feeds.push(feed_id);
            }
        };

        $scope.trackable = function (feed_id) {
            feed_id = parseInt(feed_id);

            return _.indexOf($feeds, feed_id) != -1;
        };

        $scope.customFeedUrl = '';
        $rootScope.rssValidLink = true;

        $scope.addCustomRSSFeed = function (url, name) {
            $rootScope.rssValidLink = true;
            $rootScope.rssValidName = true;
            var data = {
                url: url,
                name: name
            };
            if(!url || url == 'undefined') {
                $rootScope.rssValidLink = false;
            }
            if(!name || name == 'undefined') {
                $rootScope.rssValidName = false;
            }
            if($rootScope.rssValidLink == false || $rootScope.rssValidName == false) {
                return;
            }
            FeedService.createCustomFeed(data)
                .then(function (res) {
                    document.getElementById('rss_url').value = '';
                    document.getElementById('rss_name').value = '';
                    $scope.allFeeds.push({id: res.id, name: res.name});
                }, function (err) {
                    $rootScope.rssValidLink = false;
                })
        };

    }]);
app.controller('SizerController', ['$scope', '$window', function ($scope, $window) {
    function getMultiplier() {
        if ($.browser.msie) {
            return 0.68;
        }

        return 0.8;
    }

    var resize = function() {
        var k = getMultiplier();

        var viewport = $(window).height();
        var height = Math.round(viewport * k);

        if (! angular.isMobile) {
            $scope.size1 = Math.round(height * 0.34);
            $scope.size2 = Math.round(height * 0.44);
            $scope.size3 = height - ($scope.size1 + $scope.size2);
        } else {
            $scope.size1 = 190;
            $scope.size2 = 230;
            $scope.size3 = 140;
        }

        $scope.resized = true;
    };
    setTimeout(resize, 100);

    $(window).on('resize', resize);
}]);
app.controller('WeatherController', [
    '$scope', '$timeout', 'WeatherService', 'GeoService', 'localStorageService', '$http',
    function ($scope, $timeout, WeatherService, GeoService, localStorageService, $http) {
        var filterChanged = false, savedFilter;

        var defaultFilter = {
            units: 'si',
            location: {},
            address: ""
        };

        $scope.cities = [];

        $scope.filter = angular.copy(defaultFilter);

        $scope.weather = {};

        $scope.loading = false;

        function searchForCity(name) {
            $http.get(app.API_PREFIX + '/geo/places?name=' + name)
                .then(function (response) {
                    var cities = _.uniq(response.data.predictions) || [];

                    $scope.cities = cities;
                });
        }

        // skipTracking used when city is predicted by Places API and directly inserted into filter.location
        // so to prevent double checking, temporary skip this step
        var skipTracking = false;

        function addressModified(n1, n2) {
            return n1.address !== n2.address && n1.address.length >= 3;
        }

        $scope.$watch('filter', function (n1, n2) {
            if (skipTracking || n1 === n2) return false;
            filterChanged = true;

            if (addressModified(n1, n2)) {
                searchForCity(n1.address);
            }

            // restore tracking:
            skipTracking = false;
        }, true);

        function restoreSavedFilter() {
            delayFilterTracking();

            $scope.filter = angular.copy(defaultFilter);
        }

        function finish(cb) {
            if (cb) {
                cb();
            }
        }

        function cacheFilter() {
            localStorageService.set('w_fltr', JSON.stringify(_.omit($scope.filter, 'address')));

            defaultFilter = angular.copy($scope.filter);
        }

        function loadCachedFilter() {
            return JSON.parse(localStorageService.get('w_fltr'));
        }

        function currentLocation() {
            return [
                $scope.filter.location.lat,
                $scope.filter.location.lng
            ].join(",")
        }

        // when location or units did change => fetch new weather and set to cache
        $scope.$on('location.changed', function () {
            WeatherService.fetch(currentLocation(), {units: $scope.filter.units}).then(function (results) {
                $scope.weather = angular.extend(results, $scope.filter);

                var currnetHour = new Date().getHours();
                var currentDate = new Date().getDate();
                var counter = 1;
                for (var i in $scope.weather.hourly.data) {

                    var time = new Date($scope.weather.hourly.data[i].time);
                    var hour = time.getHours();
                    var date = time.getDate();
                    if(currentDate == date && currnetHour == hour) {
                        $scope.currentHourSummary = $scope.weather.hourly.data[i].summary;
                        $scope.currentHourIcon = $scope.icon($scope.weather.hourly.data[i].icon);
                        $scope.currnetHourTemperature = $scope.weather.hourly.data[i].temperature;
                        $scope.startIndex = counter + 24;
                    }


                    counter++;
                }
                $scope.city = $scope.weather.address.split(',')[0];
            });
        });

        if (!(savedFilter = loadCachedFilter())) {
            GeoService.geolocate().then(function (GeoService) {
                $scope.filter.location = {
                    lat: GeoService.getLatitude(),
                    lng: GeoService.getLongitude()
                };

                lookup();
            });
        } else {
            $scope.filter = angular.copy(savedFilter);
            defaultFilter = angular.copy($scope.filter);

            lookup(savedFilter.location.lat, savedFilter.location.lng);
        }

        function lookup(lat, lng) {
            GeoService.lookup(lat || GeoService.getLatitude(), lng || GeoService.getLongitude()).then(function (result) {
                delayFilterTracking();
                $scope.filter.address = result.formatted_address;

                cacheFilter();

                $scope.$emit('location.changed');
            });
        }

        $scope.cancel = function (callback) {
            restoreSavedFilter();

            finish(callback);
        };

        /**
         * Save module preferences
         * @returns {boolean}
         */
        $scope.savePreferences = function (callback) {
            if (!filterChanged)
                return false;

            if ($scope.loading)
                return false;

            $scope.loading = true;

            if (filterChanged && $scope.filter.address.length) {
                filterChanged = false;
                GeoService.geocode($scope.filter.address).then(function (result) {
                    if (result && result.hasOwnProperty('geometry')) {
                        delayFilterTracking();

                        $scope.filter = angular.extend($scope.filter, {
                            // address: result.formatted_address,
                            location: result.geometry.location
                        });

                        cacheFilter();

                        $scope.$emit('location.changed');

                        finish(callback);
                    }

                    $scope.loading = false;
                });
            } else {
                $scope.$emit('location.changed');

                finish(callback);
            }

            return false;
        };

        $scope.locationToCity = function (address) {
            if (!address || !address.indexOf(',')) return '';

            return _.first(
                address.split(', ')
            );
        };

        function delayFilterTracking() {
            skipTracking = true;

            $timeout(function () {
                skipTracking = false;
            }, 100);
        }

        $scope.selectCity = function (city) {
            delayFilterTracking();

            $scope.filter.address = city.description;

            $scope.cities = null;
        };

        $scope.icon = function (icon) {
            return app.REWRITE_BASE + 'images/icons/w/' + icon + '.png';
        }

        $scope.showThisHour = function (currentHourData) {
            var currnetHour = new Date().getHours();
            var currentDate = new Date().getDate();
            var time = new Date(currentHourData.time);
            var hour = time.getHours();
            var date = time.getDate();
            if(currentDate == date && currnetHour < hour) {
                return true;
            } else if(currentDate < date) {
                return true;
            } else {
                return false;
            }
        };

        $scope.getTimeToDate = function (time) {
            return new Date(time);
        }
    }]);
app.directive('cardBox', ['$timeout', '$rootScope', function ($timeout, $rootScope) {
    return {
        'restrict': "E",
        'scope': {
            'title': "@"
        },
        'transclude': {
            'actions': '?cardBoxActions',
            'body': 'cardBoxBody'
        },
        'link': function (scope, element) {
            scope.editable = false;

            /**
             * toggle the actions button if no actions content provided
             * @type {boolean}
             */
            $timeout(function () {
                scope.hasActions = !!element.find('card-box-actions').text().length;
            });

            /**
             * Toggle box's preferences
             */
            scope.switchEditableMode = function (callback) {

                scope.editable = !scope.editable;


                if (callback) {
                    callback();
                }
            };

            function close() {

                if(Object.keys($rootScope.eventError).length > 0) {
                    if($rootScope.eventError.invalidTime) {
                        document.getElementById("custom-event-hours").value = '';
                        document.getElementById("custom-event-minutes").value = '';
                        document.getElementById("custom-event-seconds").value = '';
                    }
                    $rootScope.eventError = {};
                }

                $rootScope.rssValidLink = true;
                var datePickerOpen = document.getElementsByClassName("datepicker");
                if(datePickerOpen.length > 0) {
                    return;
                }
                angular.safeApply(scope, function (scope) {
                    scope.editable = false;
                });
            }

            scope.close = close;

            $rootScope.$on('cardbox.close', close);
        },
        'templateUrl': app.REWRITE_BASE + 'assets/templates/card-box.html'
    };
}]);
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
/*global angular */
(function (angular) {
    'use strict';
    angular.module('countdownTimer', [])
        .directive('countdown', function ($interval, dateFilter) {
            return {
                restrict: 'A',
                transclude: true,
                link: function (scope, element, attrs) {
                    var countDownInterval;

                    function displayString() {
                        
                        attrs.units = angular.isArray(attrs.units) ?  attrs.units : attrs.units.split('|');
                        var lastUnit = attrs.units[attrs.units.length - 1],
                            unitConstantForMillisecs = {
                                weeks: (1000 * 60 * 60 * 24 * 7),
                                days: (1000 * 60 * 60 * 24),
                                hours: (1000 * 60 * 60),
                                minutes: (1000 * 60),
                                seconds: 1000,
                                milliseconds: 1
                            },
                            unitsLeft = {},
                            returnString = '',
                            totalMillisecsLeft = new Date(attrs.endDate) - new Date(),
                            i,
                            unit;
                        for (i in attrs.units) {
                            if (attrs.units.hasOwnProperty(i)) {
                                //validation
                                unit = attrs.units[i].trim();
                                if (unitConstantForMillisecs[unit.toLowerCase()] === false) {
                                    $interval.cancel(countDownInterval);
                                    throw new Error('Cannot repeat unit: ' + unit);

                                }
                                if (unitConstantForMillisecs.hasOwnProperty(unit.toLowerCase()) === false) {
                                    $interval.cancel(countDownInterval);
                                    throw new Error('Unit: ' + unit + ' is not supported. Please use following units: weeks, days, hours, minutes, seconds, milliseconds');
                                }

                                //saving unit left into object
                                unitsLeft[unit] = totalMillisecsLeft / unitConstantForMillisecs[unit.toLowerCase()];

                                //precise rounding
                                if (lastUnit === unit) {
                                    unitsLeft[unit] = Math.ceil(unitsLeft[unit]);
                                } else {
                                    unitsLeft[unit] = Math.floor(unitsLeft[unit]);
                                }
                                //updating total time left
                                totalMillisecsLeft -= unitsLeft[unit] * unitConstantForMillisecs[unit.toLowerCase()];
                                //setting this value to false for validation of repeated units
                                unitConstantForMillisecs[unit.toLowerCase()] = false;
                                //adding verbage

                                returnString += ' ' + unitsLeft[unit] + ' ' + unit;
                                
                            }
                        }
                        return returnString;
                    }
                    function updateCountDown() {
                        element.text(displayString());
                    }

                    element.on('$destroy', function () {
                        $interval.cancel(countDownInterval);
                    });

                    countDownInterval = $interval(function () {
                        updateCountDown();
                    }, 1);
                }
            };
        });
}(angular));
app.factory('CustomEventService', ['$http', '$httpParamSerializer', function ($http, $httpParamSerializer) {
    var factory = {};

    factory.createEvent = function (data) {

        return $http.post(app.API_PREFIX + '/custom-event', data)
            .then(function (response) {
                return response.data.data;
            });
    };

    factory.getEvent = function () {
        return $http.get(app.API_PREFIX + '/custom-event')
            .then(function (response) {
                return response.data.data;
            })
    };

    factory.updateEvent = function (data) {
        return $http.post(app.API_PREFIX + '/custom-event/' + data.id, data)
            .then(function (response) {
                return response.data.data;
            });
    };

    return factory;
}]);
app.factory('EventsService', ['$http', '$httpParamSerializer', function ($http, $httpParamSerializer) {
    var factory = {};

    factory.events = function (calendar) {
        var args = $httpParamSerializer({
            'c': calendar,
            't': (new Date).getTime(),
            'tz' : 0//(new Date).getTimezoneOffset()
        });
        return $http.get(app.API_PREFIX + '/calendar/events?' + args)
            .then(function (response) {
                return response.data.data;
            });
    };

    return factory;
}]);
app.factory('FeedService', ['$http', '$httpParamSerializer', function ($http, $httpParamSerializer) {
    var factory = {};

    factory.news = function (feeds) {
        var args = $httpParamSerializer({
            ids: feeds.join(',')
        });
        return $http.get(app.API_PREFIX + '/feed/news?' + args)
            .then(function (response) {
                return response.data.data;
            });
    };

    factory.createCustomFeed = function (data) {
        return $http.post(app.API_PREFIX + '/feed', data)
            .then(function (res) {
               return res.data.data;
            })
    };

    return factory;
}]);
app.factory('GeoService', ['$q', '$http', function ($q, $http) {
    var factory = {
        lat: null,
        lng: null
    };

    factory.setLocation = function (lat, lng) {
        factory.lat = parseFloat(lat);
        factory.lng = parseFloat(lng);

        return factory;
    };

    factory.getLatitude = function () {
        return this.lat;
    };

    factory.getLongitude = function () {
        return this.lng;
    };

    function setDefaultLocation() {
        factory.setLocation(
            40.7127837,
            -74.0059413
        );

        return factory;
    }

    function fetchLocationUsingIP(defer) {
        $http.get(app.API_PREFIX + '/geo/ip').then(function (response) {
            var data = response.data;
            if (data.cityName.length && '-' != data.cityName) {
                factory.setLocation(
                    data.latitude,
                    data.longitude
                );
                defer.resolve(factory);
            } else {
                defer.resolve(
                    setDefaultLocation()
                );
            }
        }).catch(function () {
            defer.resolve(
                setDefaultLocation()
            );
        });
    }

    /**
     * Locate the client by asking Navigator.GeoLocation.
     */
    factory.geolocate = function () {
        var defer = $q.defer();

        // setTimeout(function () {
        //     return fetchLocationUsingIP(defer);
        // }, 5000);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                factory.setLocation(
                    position.coords.latitude,
                    position.coords.longitude
                );

                defer.resolve(factory);
            }, function () {
                return fetchLocationUsingIP(defer);
            });
        } else {
            // set default location to new york
            defer.resolve(
                setDefaultLocation()
            );
        }

        return defer.promise;
    };

    factory.geocode = function (location) {
        return $http.get(app.API_PREFIX + '/geo/code?loc=' + location)
            .then(function (response) {
                return response.data.results[0];
            });
    };

    factory.lookup = function (lat, lng) {
        return $http.get(app.API_PREFIX + '/geo/lookup?latlng=' + [lat, lng].join(','))
            .then(function (response) {
                return response.data.results[0];
            });
    };

    return factory;
}]);
app.factory('GmailService', ['$http', '$httpParamSerializer', function ($http, $httpParamSerializer) {
    var factory = {};

    /**
     * Fetch the messages list that match criteria.
     *
     * @param args
     * @returns {*}
     */
    factory.fetchMessages = function (args) {
        return $http.get(app.API_PREFIX + '/gmail/messages?' + $httpParamSerializer(args))
            .then(function (response) {
                return response.data;
            });
    };

    /**
     * Fetch the message.
     *
     * @param messageId
     * @returns {*}
     */
    factory.get = function (messageId) {
        return $http.get(app.API_PREFIX + '/gmail/messages/' + messageId + '?include=body')
            .then(function (response) {
                return response.data;
            });
    };

    /**
     * Mark message as Read.
     *
     * @param messageId
     * @returns {*}
     */
    factory.markAsRead = function (messageId) {
        return $http.get(app.API_PREFIX + '/gmail/messages/' + messageId + '/touch');
    };

    return factory;
}]);
app.factory("WeatherService", ['$http', '$httpParamSerializer', function ($http, $httpParamSerializer) {
    var factory = {};

    factory.fetch = function (coords, params) {
        var $args = angular.extend({
            coords: coords,
            units: 'si'
        }, params || {});

        var $url = app.API_PREFIX + '/weather/get?' + $httpParamSerializer($args);
        return $http
            .get($url)
            .then(function (response) {
                return response.data;
            });
    };

    return factory;
}]);
//# sourceMappingURL=app.js.map
