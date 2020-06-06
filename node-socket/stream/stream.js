const mysql = require('mysql');
const connection = mysql.createConnection({
    host: 'yuserver.in',
    user: 'yuserver_yuserver',
    password: 'nazim@123',
    database: 'yuserver_stream'
});
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
            socket.to(data.room).emit('room enter', {socketId: data.socketId});
            socket.to(data.room).emit('new user', {socketId: data.socketId, username: data.username});
        }

        console.log(socket.rooms);
    });


    socket.on('newUserStart', (data) => {
        socket.to(data.to).emit('newUserStart', {sender: data.sender,username: setSocket.username});
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
        console.log(setSocket);
        if (setSocket.owner && setSocket.owner == 1) {
            let sql = "UPDATE `streams` SET `is_active` = 0 WHERE `broadcaster` = ?";
            let updateData = [setSocket.socketId];
            // execute the UPDATE statement
            connection.query(sql, updateData);
            socket.to(setSocket.room).emit('room close', {socketId: setSocket.socketId});
        }
    });
}

module.exports = stream;