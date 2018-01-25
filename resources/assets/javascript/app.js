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
        if (0 == $(event.target).closest('div.card-actions.dropdown.open').length) {
            $rootScope.$broadcast('cardboxrss.close');
        }
    }
}]);

app.REWRITE_BASE = '/';
if (location.host == 'dev-your-morning.rainbowriders.dk') {
    app.REWRITE_BASE = '/public/';
}

app.API_PREFIX = app.REWRITE_BASE + 'api/v1';
