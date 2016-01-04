var net = require('net'),
    fs = require('fs');

var lastStatus,
    socketPath = '/tmp/artd-status-updater.sock';

fs.unlink(socketPath, function () {
    var server = net.createServer(function (client) {
        console.log('Client connected');

        client.on('data', function (data) {
            var newStatus = data.toString();

            if (lastStatus !== newStatus) {
                lastStatus = newStatus;
                console.log(lastStatus);
            }
        });

        client.on('end', function () {
            console.log('Client disconnected');
        });

    });
    server.listen(socketPath, function (e) {
        console.log('server bound on %s', socketPath);
    });
});

process.on('uncaughtException', function (err) {
    console.log("UNCAUGHT EXCEPTION ");
    console.log("[Inside 'uncaughtException' event] " + err.stack || err.message);
});