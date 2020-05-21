"use strict";
const mysql = require('mysql2/promise');
module.exports = class Stream {

    constructor(io = true) {
        this.io = io;
        this.initialize();
    }

    initialize() {
        this.setupMysql();
//        this.handleSocketConnection();
    }

    async setupMysql() {
        this.connection = await  mysql.createConnection({
            host: 'localhost',
            user: 'root',
            password: '',
            database: 'live_stream'
        });
    }

    async getUser(email, stream_token) {
        let sql = 'SELECT * FROM users WHERE email = ? AND stream_token = ? ';
        let data = [email, stream_token];
        const [rows, fields] = await this.connection.execute(sql, data);
        return rows[0];
    }

    async initialStream(roomToken, broadcaster, senderId) {
        console.log('update query');
        // update statment
        let sql = "UPDATE `streams` SET `room_token` = ? , `broadcaster` = ? , `is_active` = '1' WHERE `streams`.`id` = ?";
        let data = [roomToken, broadcaster, senderId];
        console.log(data);
        // execute the UPDATE statement
        var response = await this.connection.query(sql, data);
        console.log(response);
    }

    async getStream(stream_token, sender_token) {
        let sql = 'SELECT * FROM streams WHERE request_token = ? AND  user_id = ? ';
        let data = [stream_token, sender_token];
        const [rows, fields] = await this.connection.execute(sql, data);
        return rows[0];
    }
}