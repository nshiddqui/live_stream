<!-- script used to stylize video element -->
<script src="https://www.webrtc-experiment.com/getMediaElement.min.js"></script>

<script src="https://www.webrtc-experiment.com/socket.io.js"></script>
<script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
<script src="https://www.webrtc-experiment.com/IceServersHandler.js"></script>
<script src="https://www.webrtc-experiment.com/CodecsHandler.js"></script>
<script src="https://www.webrtc-experiment.com/RTCPeerConnection-v1.5.js"></script>
<script src="https://www.webrtc-experiment.com/video-conferencing/conference.js"></script>
<script>
    var config = {
        openSocket: function (config) {
            var SIGNALING_SERVER = 'https://localhost:9559/';

            config.channel = config.channel || '<?= $stream_data->request_token ?>';
//            console.log(config.channel);
            var sender = '<?= $current_user['stream_token'] ?>';
            var email = '<?= $current_user['email'] ?>';
<?php if ($current_user['id'] === $stream_data->user_id) { ?>
                io.connect(SIGNALING_SERVER).emit('new-channel', {
                    channel: config.channel,
                    sender: sender,
                    email: email
                });
<?php } ?>
            setTimeout(function () {
                var socket = io.connect(SIGNALING_SERVER + config.channel);
                console.log(socket);
                console.log(config.channel);
                socket.channel = config.channel;
                socket.on('connect', function () {
                    if (config.callback)
                        config.callback(socket);
                });
                socket.send = function (message) {
                    console.log(message);
                    socket.emit('message', {
                        sender: sender,
                        data: message
                    });
                };
                socket.on('message', config.onmessage);
            }, 3000);
        },
        onRemoteStream: function (media) {
            var mediaElement = getMediaElement(media.video, {width: (videosContainer.clientWidth / 2) - 50,
                buttons: ['mute-audio', 'mute-video', 'full-screen', 'volume-slider']
            });
            mediaElement.id = media.stream.streamid;
            videosContainer.appendChild(mediaElement);
        },
        onRemoteStreamEnded: function (stream, video) {
            if (video.parentNode && video.parentNode.parentNode && video.parentNode.parentNode.parentNode) {
                video.parentNode.parentNode.parentNode.removeChild(video.parentNode.parentNode);
            }
        },
//        onRoomFound: function (room) {
//            var alreadyExist = document.querySelector('button[data-broadcaster="' + room.broadcaster + '"]');
//            if (alreadyExist)
//                return;
//
//            if (typeof roomsList === 'undefined')
//                roomsList = document.body;
//
//            var tr = document.createElement('tr');
//            tr.innerHTML = '<td><strong>' + room.roomName + '</strong> shared a conferencing room with you!</td>' +
//                    '<td><button class="join">Join</button></td>';
//            roomsList.appendChild(tr);
//
//            var joinRoomButton = tr.querySelector('.join');
//            joinRoomButton.setAttribute('data-broadcaster', room.broadcaster);
//            joinRoomButton.setAttribute('data-roomToken', room.roomToken);
//            joinRoomButton.onclick = function () {
//                this.disabled = true;
//
//                var broadcaster = this.getAttribute('data-broadcaster');
//                var roomToken = this.getAttribute('data-roomToken');
//                captureUserMedia(function () {
//                    conferenceUI.joinRoom({
//                        roomToken: roomToken,
//                        joinUser: broadcaster
//                    });
//                }, function () {
//                    joinRoomButton.disabled = false;
//                });
//            };
//        },
//        onRoomClosed: function (room) {
//            var joinButton = document.querySelector('button[data-roomToken="' + room.roomToken + '"]');
//            if (joinButton) {
//                // joinButton.parentNode === <li>
//                // joinButton.parentNode.parentNode === <td>
//                 joinButton.parentNode.parentNode.parentNode === <tr>
//                 joinButton.parentNode.parentNode.parentNode.parentNode === <table>
//                joinButton.parentNode.parentNode.parentNode.parentNode.removeChild(joinButton.parentNode.parentNode.parentNode);
//            }
//        },
        onReady: function () {
            console.log('now you can open or join rooms');
        }
    };
<?php if ($current_user['id'] === $stream_data->user_id) { ?>
        $(document).ready(function () {
            startStream();
        });

        function startStream() {
            captureUserMedia(function () {
                conferenceUI.createRoom({
                    roomName: '<?= $stream_data->title ?>' || 'Anonymous'
                });
            }, function (response) {
                console.log(response);//do later
            });
        }
<?php } else if ($current_user['id'] === $stream_data['stream_detail']->user_id) { ?>
        $(document).ready(function () {
            joinStream();
        });

        function joinStream() {
            var broadcaster = '<?= $stream_data->broadcaster ?>';
            var roomToken = '<?= $stream_data->room_token ?>';
            captureUserMedia(function () {
                conferenceUI.joinRoom({
                    roomToken: roomToken,
                    joinUser: broadcaster
                });
            }, function () {                 //do later
            });
        }
<?php } ?>

    function captureUserMedia(callback, failure_callback) {
        var video = document.createElement('video');
        video.muted = true;
        video.volume = 0;

        try {
            video.setAttributeNode(document.createAttribute('autoplay'));
            video.setAttributeNode(document.createAttribute('playsinline'));
            video.setAttributeNode(document.createAttribute('controls'));
        } catch (e) {
            video.setAttribute('autoplay', true);
            video.setAttribute('playsinline', true);
            video.setAttribute('controls', true);
        }

        getUserMedia({
            video: video,
            onsuccess: function (stream) {
                config.attachStream = stream;

                var mediaElement = getMediaElement(video, {width: (videosContainer.clientWidth / 2) - 50,
                    buttons: ['mute-audio', 'mute-video', 'full-screen', 'volume-slider']
                });
                mediaElement.toggle('mute-audio');
                videosContainer.appendChild(mediaElement);

                callback && callback();
            },
            onerror: function () {
                alert('unable to get access to your webcam');
                callback && callback();
            }
        });
    }
    var conferenceUI = conference(config);

    /* UI specific */
    var videosContainer = document.getElementById('videos-container') || document.body;
//    var btnSetupNewRoom = document.getElementById('setup-new-room');
    var roomsList = document.getElementById('rooms-list');

//    if (btnSetupNewRoom)
//        btnSetupNewRoom.onclick = setupNewRoomButtonClickHandler;

    function rotateVideo(video) {
        video.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(0deg)';
        setTimeout(function () {
            video.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(360deg)';
        }, 1000);
    }

    function scaleVideos() {
        var videos = document.querySelectorAll('video'),
                length = videos.length, video;

        var minus = 130;
        var windowHeight = 700;
        var windowWidth = 600;
        var windowAspectRatio = windowWidth / windowHeight;
        var videoAspectRatio = 4 / 3;
        var blockAspectRatio;
        var tempVideoWidth = 0;
        var maxVideoWidth = 0;

        for (var i = length; i > 0; i--) {
            blockAspectRatio = i * videoAspectRatio / Math.ceil(length / i);
            if (blockAspectRatio <= windowAspectRatio) {
                tempVideoWidth = videoAspectRatio * windowHeight / Math.ceil(length / i);
            } else {
                tempVideoWidth = windowWidth / i;
            }
            if (tempVideoWidth > maxVideoWidth)
                maxVideoWidth = tempVideoWidth;
        }
        for (var i = 0; i < length; i++) {
            video = videos[i];
            if (video)
                video.width = maxVideoWidth - minus;
        }
    }

    window.onresize = scaleVideos;

</script>