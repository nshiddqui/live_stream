const mysql = require('mysql');
const fs = require('fs');
const connection = mysql.createConnection({
    host: 'yuserver.in',
    user: 'yuserver_yuserver',
    password: 'nazim@123',
    database: 'yuserver_stream'
});
var data = fs.readFileSync('stream/stream_data.json'),
        streamData;
try {
    streamData = JSON.parse(data);
    console.dir(streamData);
} catch (err) {
    console.log('There has been an error parsing your JSON.')
    console.log(err);
}
const stream = (socket) => {
    let setSocket;
    socket.on('subscribe', (data) => {
        //subscribe/join a room
        socket.join(data.room);
        socket.join(data.socketId);
        let sql = "UPDATE `streams` SET `is_active` = 1 WHERE `streams`.`request_token` = ?";
        let updateData = [data.room];
        console.log(updateData);
        // execute the UPDATE statement

        connection.query(sql, updateData);
        if (!setSocket) {
            setSocket = {
                socketId: data.socketId,
                owner: data.owner,
                room: data.room,
                username: data.username
            };
        }

        //Inform other members in the room of new user's arrival
        if (socket.adapter.rooms[data.room].length > 1) {
            if (getScreenSetting() && getScreenSetting() == 'on') {
                console.log('screen setting cheked');
                socket.emit('screen sharing on', {socketId: data.socketId});
            }
            socket.to(data.room).emit('room enter', {socketId: data.socketId});
            socket.to(data.room).emit('new user', {socketId: data.socketId, username: data.username});
        }

        console.log(socket.rooms);
    });


    socket.on('newUserStart', (data) => {
        socket.to(data.to).emit('newUserStart', {sender: data.sender, username: setSocket.username});
    });

    socket.on('screen sharing off', (data) => {
        updateScreenSetting('off');
        console.log('screen off');
        socket.to(setSocket.room).emit('screen sharing off', data);
    });

    socket.on('screen sharing on', (data) => {
        updateScreenSetting('on');
        console.log('screen on');
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
        console.log('disconnect');
        console.log(setSocket);
        if (setSocket.owner && setSocket.owner == 1) {
            let sql = "UPDATE `streams` SET `is_active` = '0' WHERE `request_token` = ?";
            let updateData = [setSocket.room];
            // execute the UPDATE statement
            connection.query(sql, updateData);
            socket.to(setSocket.room).emit('room close', {socketId: setSocket.socketId});
        }
    });

    function updateScreenSetting(value) {
        initializeData();
        console.log(streamData[setSocket.room]);
        console.log(streamData[setSocket.room][0]);
        streamData[setSocket.room].screen_setting = value;
        modifyData();
    }

    function getScreenSetting() {
        initializeData();
        return streamData[setSocket.room].screen_setting;
    }

    function initializeData() {
        if (!streamData[setSocket.room]) {
            connection.query("SELECT * FROM streams WHERE request_token = ?", [setSocket.room], function (err, result, fields) {
                if (err)
                    throw err;
                console.log(result);
                streamData[data.room] = result;
                modifyData();
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
            console.log('Configuration saved successfully.')
        });
    }
}

module.exports = stream;