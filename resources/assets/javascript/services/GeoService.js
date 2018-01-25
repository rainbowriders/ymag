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