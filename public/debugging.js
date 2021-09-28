(function () {
    if (!REMOTE_DEBUG) {
        return;
    }

    var oldError = console.error;
    console.error = (...args) => {
        for (var x=0;x<args.length;x++) {
            oldError(args[x]);

            log(args[x]);
        }
    };

    function log(error) {
        if (error instanceof Error) {
            error = error.toString();
        } else if (typeof error === 'object') {
            error = JSON.stringify(error);
        } else if (typeof error !== 'string') {
            error = error + '';
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", '/debug/log', false);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify({
            entry: error,
            href: window.location.pathname
        }));
    }

    function stack() {
        var err = new Error();
        return err.stack;
    }
})();
