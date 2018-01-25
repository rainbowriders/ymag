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