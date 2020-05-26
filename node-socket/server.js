
var fs = require('fs');
const stream = require('./stream');
var streamSql = new stream;
// don't forget to use your own keys!
var options = {
    key: fs.readFileSync("keys/keys/bec0a_534df_6e5c5b8983794f644de16088823d373a.key"),
    cert: fs.readFileSync("keys/certs/yuserver_in_bec0a_534df_1597881599_06dd75f36b1d1a70af1d4ea63c88b73c.crt"),
    // key: fs.readFileSync('keys/privatekey.pem'),
    // cert: fs.readFileSync('keys/certificate.pem')
};

// HTTPs server
var app = require('https').createServer(options, function (request, response) {
    response.writeHead(200, {
        'Content-Type': 'text/html'
    });
    response.write("<h1>Live Stream Server Working.</h1>");
    response.end();
});


// socket.io goes below

var io = require('socket.io').listen(app, {
    log: false,
    origins: '*:*'
});

io.set('transports', [
    // 'websocket',
    'xhr-polling',
    'jsonp-polling'
]);

var channels = {};

io.sockets.on('connection', function (socket) {
    var initiatorChannel = '';
    var userDetails = false;
    var streamDetails
    if (!io.isConnected) {
        io.isConnected = true;
    }

    socket.on('new-channel', async function (data) {
        socket.emit('response', 'Initializing');
        if (!channels[data.channel] || userDetails === false) {
            initiatorChannel = data.channel;
//            userDetails = await streamSql.getUser(data.email, data.sender);
//            if (userDetails === false) {
//                console.log('User Not Exist');
//                socket.emit('response', 'User Not Exist');
//                socket.emit('disconnect');
//                return false;
//            }
        } else {
            console.log('Already User');
        }
//        streamDetails = await streamSql.getStream(data.channel, userDetails.id);
//        if (streamDetails === false) {
//            console.log('Stream Not Exist');
//            socket.emit('response', 'Stream Not Exist');
//            socket.emit('disconnect');
//            return false;
//        }
//        if (!channels[data.channel]) {
        channels[data.channel] = data.channel;
        onNewNamespace(data.channel, data.sender, streamDetails);
//        }
    });

    socket.on('presence', function (channel) {
        var isChannelPresent = !!channels[channel];
        socket.emit('presence', isChannelPresent);
    });

    socket.on('disconnect', function (channel) {
        console.log('disconnect');
        if (initiatorChannel) {
            delete channels[initiatorChannel];
        }
    });
});

function onNewNamespace(channel, sender, streamDetails) {
    io.of('/' + channel).on('connection', function (socket) {
        var username;
        var initialStream = true;
        if (io.isConnected) {
            io.isConnected = false;
            socket.emit('connect', true);
        }

        socket.on('message', function (data) {
            if (data.sender == sender) {
                if (!username) {
                    username = data.data.sender;
//                    if (initialStream) {
//                        streamSql.initialStream(data.data.roomToken, data.data.broadcaster, streamDetails.id);
//                        initialStream = false;
//                    }
                }
                socket.broadcast.emit('message', data.data);
            }
        });

        socket.on('disconnect', function () {
            console.log('disconnet user');
            if (username) {
                socket.broadcast.emit('user-left', username);
                username = null;
            }
        });
    });
}

// run app

app.listen(process.env.PORT || 9559);

process.on('unhandledRejection', (reason, promise) => {
    console.log(reason);
});

console.log('Please open SSL URL: https://yuserver.in:' + (process.env.PORT || 9559) + '/');
