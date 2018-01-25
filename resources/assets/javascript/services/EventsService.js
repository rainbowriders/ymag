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