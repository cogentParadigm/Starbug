define(['dojo/request/registry', 'dojo/when'], function (registry, when) {
    var mocking = false,
        handles = [];

    function start() {
        if (mocking) return;

        mocking = true;

        // Set up a handler for requests to '/info' that mocks a
        // response without requesting from the server at all
        handles.push(
            registry.register(/api\//, function (url, options) {
                // Wrap using `when` to return a promise;
                // you could also delay the response
                var parts = url.split('/');
                while (parts[0] !== "api") parts.shift();
                var model = parts[1];
                var action = parts[2];
                var records = [];
                if (action == "errors") {
                  records = {errors:[{field:'first_name', errors:{required:'This field is required'}}]};
                } else if (model == "users") {
                  records.push({id:1,first_name:'Ali',last_name:'Gangji'});
                }
                return when(records);
            })
        );
    }

    function stop() {
        if (!mocking) {
            return;
        }

        mocking = false;

        var handle;

        while ((handle = handles.pop())) {
            handle.remove();
        }
    }

    return {
        start: start,
        stop: stop
    };
});
