app.directive('cardBoxRss', ['$timeout', '$rootScope', function ($timeout, $rootScope) {
    return {
        'restrict': "E",
        'scope': {
            'title': "@"
        },
        'transclude': {
            'actions': '?cardBoxRssActions',
            'body': 'cardBoxRssBody'
        },
        'link': function (scope, element) {
            scope.editable = false;

            /**
             * toggle the actions button if no actions content provided
             * @type {boolean}
             */
            $timeout(function () {
                scope.hasActions = !!element.find('card-box-rss-actions').text().length;
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
                if($rootScope.closeRssSettings == false) {
                    $rootScope.closeRssSettings = true;
                    return;
                }
                $rootScope.rssValidLink = true;
                angular.safeApply(scope, function (scope) {
                    scope.editable = false;
                });
            }

            scope.close = close;

            $rootScope.$on('cardboxrss.close', close);
        },
        'templateUrl': app.REWRITE_BASE + 'assets/templates/card-box-rss.html'
    };
}]);