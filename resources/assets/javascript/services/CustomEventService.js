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