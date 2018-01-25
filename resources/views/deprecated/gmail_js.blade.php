<script>
    function checkAuth() {
        gapi.auth.authorize({
            'client_id': '{{ config('services.google.client_id') }}',
            'scope': '{{ join(" ", [
                'https://www.googleapis.com/auth/calendar.readonly',
                'https://www.googleapis.com/auth/gmail.readonly'
            ]) }}',
            'immediate': true
        }, handleAuthResult);
    }

    function handleAuthResult(authResult) {
        if (authResult && !authResult.error) {
            loadGmailApi();
        } else {
            // Show auth UI, allowing the user to initiate authorization by
            // clicking authorize button.
            alert('Auth error');
        }
    }

    function loadGmailApi() {
        gapi.client.load('gmail', 'v1', listLabels);
    }

    function listLabels() {
        var request = gapi.client.gmail.users.messages.list({
            'userId': 'me',
            'maxResults': 10
        });

        request.execute(function (resp) {
            var messages = resp.messages;
            console.log('Messages:');

            if (messages && messages.length > 0) {
                for (i = 0; i < messages.length; i++) {
                    getMessage(messages[i].id, function (message) {
                        messages[i] = $.extend(messages[i], message.result);
                        console.log(messages[i]);
                    });
                }
            } else {
                console.log('No Messages found.');
            }
        });
    }

    function getMessage(id, callback) {
        var request = gapi.client.gmail.users.messages.get({
            'id': id,
            'userId': 'me',
            'format': 'full'
        });

        request.execute(callback);
    }
</script>

<script src="https://apis.google.com/js/client.js?onload=checkAuth"></script>