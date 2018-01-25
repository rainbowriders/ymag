app.controller('SizerController', ['$scope', '$window', function ($scope, $window) {
    function getMultiplier() {
        if ($.browser.msie) {
            return 0.695;
        }
        return 0.815;
    }

    var resize = function() {
        var k = getMultiplier();

        var viewport = $(window).height();
        var height = Math.round(viewport * k);
        if (! angular.isMobile) {
            if(viewport > 800) {
                $scope.size1 = Math.round(height * 0.35);
                $scope.size2 = Math.round(height * 0.45);
                $scope.size3 = height - ($scope.size1 + $scope.size2);
            } else if (viewport > 660) {
                $scope.size1 = Math.round(height * 0.35);
                $scope.size2 = Math.round(height * 0.42);
                $scope.size3 = height - ($scope.size1 + $scope.size2 + 30);
            } else {
                $scope.size1 = Math.round(height * 0.33);
                $scope.size2 = Math.round(height * 0.38);
                $scope.size3 = height - ($scope.size1 + $scope.size2 + 55);
            }

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