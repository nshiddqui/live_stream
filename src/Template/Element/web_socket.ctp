<!-- script used to stylize video element -->
<?= $this->Html->component('web-rtc/getMediaElement') ?>
<?= $this->Html->component('web-rtc/getMediaElement', 'script') ?>

<?= $this->Html->component('web-rtc/socket.io', 'script') ?>
<?= $this->Html->component('web-rtc/adapter-latest', 'script') ?>
<?= $this->Html->component('web-rtc/IceServersHandler', 'script') ?>
<?= $this->Html->component('web-rtc/CodecsHandler', 'script') ?>
<?= $this->Html->component('web-rtc/RTCPeerConnection-v1.5', 'script') ?>
<?= $this->Html->component('web-rtc/conference', 'script') ?>
<script>
    var notExecute = true;
    var CurrentRoom = [];
    var config = {
        openSocket: function (config) {
            var SIGNALING_SERVER = 'https://yuserver.in:9559/';

            config.channel = config.channel || '<?= $stream_data['stream']->request_token ?>';
            var sender = '<?= $current_user['stream_token'] ?>';

            io.connect(SIGNALING_SERVER).emit('new-channel', {
                channel: config.channel,
                sender: sender
            });

            var socket = io.connect(SIGNALING_SERVER + config.channel);
            socket.channel = config.channel;
            socket.on('connect', function () {
                if (config.callback)
                    config.callback(socket);
            });

            socket.send = function (message) {
                socket.emit('message', {
                    sender: sender,
                    data: message
                });
            };

            socket.on('message', config.onmessage);
        },
        onRemoteStream: function (media) {
            var mediaElement = getMediaElement(media.video, {
                width: (videosContainer.clientWidth / 2) - 50,
                buttons: ['mute-audio', 'mute-video', 'full-screen']
            });
            mediaElement.id = media.stream.streamid;
            videosContainer.appendChild(mediaElement);
        },
        onRemoteStreamEnded: function (stream, video) {
            if (video.parentNode && video.parentNode.parentNode && video.parentNode.parentNode.parentNode) {
                video.parentNode.parentNode.parentNode.removeChild(video.parentNode.parentNode);
            }
        },
        onRoomFound: function (room) {
            if (CurrentRoom.broadcaster && CurrentRoom.roomToken) {
                return;
            }
            CurrentRoom.broadcaster = room.broadcaster;
            CurrentRoom.roomToken = room.roomToken;
        },
        onRoomClosed: function (room) {
            if (CurrentRoom.broadcaster == room.broadcaster && CurrentRoom.roomToken == room.roomToken) {
                alert('Room is closed.');
            }
        },
        onReady: function () {
            if (notExecute) {
                notExecute = false;
<?php if ($current_user['id'] === $stream_data['stream']->user_id) { ?>
                    captureUserMedia(function () {
                        conferenceUI.createRoom({
                            roomName: '<?= $stream_data['stream']->verify_token ?>' || 'Anonymous'
                        });
                    }, function () {
                        //do later
                    });
<?php } else if ($current_user['id'] === $stream_data->user_id) { ?>
                    setTimeout(function () {
                        if (CurrentRoom.broadcaster && CurrentRoom.roomToken) {
                            var broadcaster = CurrentRoom.broadcaster;
                            var roomToken = CurrentRoom.roomToken;
                            captureUserMedia(function () {
                                conferenceUI.joinRoom({
                                    roomToken: roomToken,
                                    joinUser: broadcaster
                                });
                            }, function () {
                                //do later
                            });
                        } else {
                            alert('Stream not started yet');
                            window.location.href = '/dashboard';
                        }
                    }, 3000);
<?php } ?>
            }
        }
    };

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

                var mediaElement = getMediaElement(video, {
                    width: 150,
                    buttons: []
                });
                mediaElement.toggle('mute-audio');
                document.getElementById('streamer-container').appendChild(mediaElement);

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
    function rotateVideo(video) {
        video.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(0deg)';
        setTimeout(function () {
            video.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(360deg)';
        }, 1000);
    }

    (function () {
        var uniqueToken = document.getElementById('unique-token');
        if (uniqueToken)
            if (location.hash.length > 2)
                uniqueToken.parentNode.parentNode.parentNode.innerHTML = '<h2 style="text-align:center;display: block;"><a href="' + location.href + '" target="_blank">Right click to copy & share this private link</a></h2>';
            else
                uniqueToken.innerHTML = uniqueToken.parentNode.parentNode.href = '#' + (Math.random() * new Date().getTime()).toString(36).toUpperCase().replace(/\./g, '-');
    })();

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