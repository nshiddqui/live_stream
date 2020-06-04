const mysql = require('mysql');
const connection = mysql.createConnection({
    host: 'yuserver.in',
    user: 'yuserver_yuserver',
    password: 'nazim@123',
    database: 'yuserver_stream'
});
const stream = (socket) => {
    let roomOwner = [];
    socket.on('subscribe', (data) => {
        //subscribe/join a room
        socket.join(data.room);
        socket.join(data.socketId);
        let sql = "UPDATE `streams` SET  `is_active` = 1 WHERE `streams`.`request_token` = ?";
        let updateData = [data.room];
        console.log(updateData);
        // execute the UPDATE statement
        connection.query(sql, updateData);
        
        //Assign Room Owner
        if(!roomOwner[data.room]){
            roomOwner[data.room] = data.socketId;
        }
        
        //Inform other members in the room of new user's arrival
        if (socket.adapter.rooms[data.room].length > 1) {
            socket.to(data.room).emit('owner socket', {socketId: roomOwner[data.room]});
            socket.to(data.room).emit('new user', {socketId: data.socketId});
        }

        console.log(socket.rooms);
    });


    socket.on('newUserStart', (data) => {
        socket.to(data.to).emit('newUserStart', {sender: data.sender});
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
}

module.exports = stream;