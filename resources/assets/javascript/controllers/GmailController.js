app.controller('GmailController', ['$scope', 'GmailService', '$sce', 'localStorageService',
    function ($scope, GmailService, $sce, localStorageService) {
        $scope.searchMode = true;

        $scope.message = null;

        $scope.messagesLowThanTen = false;

        $scope.loading = false;

        $scope.nextPageToken = null;

        $scope.messages = null;

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
            $scope.messages = null;
            $scope.showBackground = false;
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
                    $scope.loading = false;
                    if($scope.messages == null) {
                        $scope.messages = [];
                    }
                    // stop to push duplicated messages
                    // if(messages.messages.length < 10) {
                    //     $scope.messagesLowThanTen = true;
                    // }
                    // image background helper
                    // if(messages.messages.length == 0) {
                    //     $scope.loading = false;
                    //     $scope.showBackground = $scope.isEmailsEmpty();
                    // }
                    // restore listing view
                    angular.safeApply($scope, function ($scope) {
                        for (var i in messages.messages) {
                            // if(isFromInbox(messages.messages[i])) {
                                $scope.messages.push(messages.messages[i]);
                            // }
                        }
                        $scope.nextPageToken = messages.nextPage;
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

        $scope.$watchCollection('messages', function () {
            $scope.showBackground = $scope.isEmailsEmpty();
        });

        $scope.isEmailsEmpty = function isEmailsEmpty() {

            if(($scope.messages != null && !$scope.messages.length && $scope.message == null)) {
                return true;
            }

            return false;
        }

    }]);