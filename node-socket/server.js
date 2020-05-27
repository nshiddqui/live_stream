let express = require('express');
let app = express();
var fs = require('fs');
var options = {
//    key: fs.readFileSync("keys/keys/bec0a_534df_6e5c5b8983794f644de16088823d373a.key"),
//    cert: fs.readFileSync("keys/certs/yuserver_in_bec0a_534df_1597881599_06dd75f36b1d1a70af1d4ea63c88b73c.crt"),
    key: fs.readFileSync('keys/privatekey.pem'),
    cert: fs.readFileSync('keys/certificate.pem')
};

let server = require('https').createServer(options, app);
let io = require('socket.io')(server);
let stream = require('./stream/stream');
let path = require('path');
let favicon = require('serve-favicon')

app.use(favicon(path.join(__dirname, 'favicon.ico')));
app.use('/assets', express.static(path.join(__dirname, 'assets')));

app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});


io.of('/stream').on('connection', stream);

server.listen(3000);
