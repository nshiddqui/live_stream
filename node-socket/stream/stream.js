const path = require('path');
const mysql = require('mysql');
const fs = require('fs');
const SocketIOFile = require('socket.io-file');
const connection = mysql.createConnection({
    host: 'live.claymould.com',
    user: 'nazim',
    password: 'nazim@123',
    database: 'live_stream'
});
var data = fs.readFileSync('stream/stream_data.json'),
        streamData;
try {
    streamData = JSON.parse(data);
} catch (err) {
    console.log('There has been an error parsing your JSON.')
    console.log(err);
}
const stream = (socket) => {
    let setSocket;

    var uploader = new SocketIOFile(socket, {
        uploadDir: 'assets',
        accepts: ['text/plain', 'application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'application/msword', 'application/rtf', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet'],
        maxFileSize: 4194304,
        chunkSize: 10240,
        transmissionDelay: 0,
        overwrite: false,
        rename: function (filename, fileInfo) {
            var file = path.parse(filename);
            var fname = file.name;
            var ext = file.ext;
            return `${fname}_${Date.now()}.${ext}`;
        },
    });


    socket.on('initial', (data) => {
        //subscribe/join a room
        socket.join(data.room);
        socket.join(data.socketId);
        if (!setSocket) {
            setSocket = {
                socketId: data.socketId,
                owner: data.owner,
                room: data.room,
                username: data.username,
                profile: data.profile
            };
        }
        if (data.owner == '1') {
            initializeData(true);
        }
        if (streamData[setSocket.room]) {
            if (data.owner != '1' && getAdminJoined()) {
                socket.emit('admin join', {socketId: data.socketId, username: data.username});
            }
        } else {
            setTimeout(function () {
                if (streamData[setSocket.room] && getAdminJoined()) {
                    socket.emit('admin join', {socketId: data.socketId, username: data.username});
                }
            }, 9000);
        }
    });
    socket.on('subscribe', (data) => {
        socket.to(data.room).emit('new user', {socketId: data.socketId, username: data.username, profile: data.profile});
        if (getScreenSetting() && getScreenSetting() == 'on') {
            socket.emit('screen sharing on', {socketId: data.socketId});
        }
    });


    socket.on('newUserStart', (data) => {
        socket.to(data.to).emit('newUserStart', {sender: data.sender, username: setSocket.username, profile: setSocket.profile});
    });

    socket.on('screen sharing off', (data) => {
        updateScreenSetting('off');
        socket.to(setSocket.room).emit('screen sharing off', data);
    });

    socket.on('screen sharing on', (data) => {
        updateScreenSetting('on');
        socket.to(setSocket.room).emit('screen sharing on', data);
    });


    socket.on('sdp', (data) => {
        socket.to(data.to).emit('sdp', {description: data.description, sender: data.sender});
    });


    socket.on('ice candidates', (data) => {
        socket.to(data.to).emit('ice candidates', {candidate: data.candidate, sender: data.sender});
    });


    socket.on('chat', (data) => {
        socket.to(data.room).emit('chat', {sender: data.sender, msg: data.msg});
    });

    socket.on('disconnect', (data) => {
        if (setSocket.owner == '1') {
            let sql = "UPDATE `streams` SET `is_active` = '0' WHERE `request_token` = ?";
            let updateData = [setSocket.room];
            // execute the UPDATE statement
            connection.query(sql, updateData);
            updateAdminJoined(false);
            socket.to(setSocket.room).emit('room close', {socketId: setSocket.socketId});
        }
    });

    function updateScreenSetting(value) {
        initializeData();
        streamData[setSocket.room].screen_setting = value;
        modifyData();
    }

    function updateAdminJoined(value) {
        initializeData();
        streamData[setSocket.room].admin_joined = value;
        modifyData();
    }

    function getScreenSetting() {
        initializeData();
        return streamData[setSocket.room].screen_setting;
    }

    function getAdminJoined() {
        initializeData();
        return streamData[setSocket.room].admin_joined;
    }

    function initializeData(force = false) {
        if (!streamData[setSocket.room] || force) {
            connection.query("SELECT * FROM streams WHERE request_token = ?", [setSocket.room], function (err, result, fields) {
                if (err)
                    throw err;
                streamData[setSocket.room] = result;
                modifyData();
                if (setSocket.owner == '1') {
                    updateAdminJoined(true);
                    socket.to(setSocket.room).emit('admin join', {socketId: setSocket.socketId, username: setSocket.username});
                    socket.to(setSocket.room).emit('room enter', {socketId: setSocket.socketId});
                }
            });
    }
    }

    function modifyData() {
        var streamDataJson = JSON.stringify(streamData);

        fs.writeFile('stream/stream_data.json', streamDataJson, function (err) {
            if (err) {
                console.log('There has been an error saving your configuration data.');
                console.log(err.message);
                return;
            }
        });
    }
}

module.exports = stream;