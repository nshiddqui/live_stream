/**
 * @author Amir Sanni <amirsanni@gmail.com>
 * @date 6th January, 2020
 */
import h from './helpers.js';

window.addEventListener('load', () => {

    if (!room) {
        alert('Room Not Found');
        window.location.href = '/';
    } else if (!username) {
        alert('Username Not Found');
        window.location.href = '/';
    } else {
        let commElem = document.getElementsByClassName('room-comm');

        for (let i = 0; i < commElem.length; i++) {
            commElem[i].attributes.removeNamedItem('hidden');
        }

        var pc = [];

        let socket = io(serverUrl + '/stream');

        var StreamAdmin;
        var socketId = '';
        var myStream = '';
        var screen = '';
        var recordedStream = [];
        var mediaRecorder = '';
        var closePromt = 'Are you sure you want to close this meeting.';
        var screenSharing = true;

        let uploader = new SocketIOFileClient(socket);
        uploader.on('complete', function (fileInfo) {
            sendMsg('<a href="' + serverUrl + '/assets/' + fileInfo.name + '" target="_BLANK" style="margin-top:10px;" download><i class="fa fa-download fa-lg"></i>Download File</a>')
        });

        socket.on('connect', () => {
            //set socketId
            socketId = socket.io.engine.id;

            socket.emit('initial', {
                room: room,
                socketId: socketId,
                owner: owner,
                username: username
            });
            if (owner == '1') {
                //Get user video by default
                getAndSetUserStream();
                socket.emit('subscribe', {
                    room: room,
                    socketId: socketId,
                    owner: owner,
                    username: username
                });
            } else {
                waitingDialog.show('Please wait for admin to start meating.');
                socket.on('admin join', (data) => {
                    //Get user video by default
                    getAndSetUserStream();
                    limitTimeOut = Math.floor(Math.random() * 800) + 1100;
                    console.log(limitTimeOut);
                    setTimeout(function () {
                        waitingDialog.hide();


                        socket.emit('subscribe', {
                            room: room,
                            socketId: socketId,
                            owner: owner,
                            username: username
                        });
                    }, (limitTimeOut));
                });
            }


            socket.on('new user', (data) => {
                socket.emit('newUserStart', {to: data.socketId, sender: socketId});
                pc.push(data.socketId);
                init(true, data.socketId, data.username);
            });

            socket.on('room close', (data) => {
                waitingDialog.show('Host is logged out. He will be back in 5 minutes else the meeting will automatically end.');
                setTimeout(function () {
                    h.pauseStream();
                }, 300);
                myStream.getVideoTracks()[0].enabled = false;
                broadcastNewTracks(myStream, 'video');
                myStream.getAudioTracks()[0].enabled = false;
                broadcastNewTracks(myStream, 'audio');
                StreamAdmin = setTimeout(function () {
                    window.location.href = '/dashboard';
                }, 300000);
            });

            socket.on('room enter', (data) => {
                if (owner != '1') {
                    waitingDialog.hide();
                    h.continueStream();
                    if (myStream) {
                        if (video == '1') {
                            myStream.getVideoTracks()[0].enabled = true;
                            broadcastNewTracks(myStream, 'video');
                        }
                        myStream.getAudioTracks()[0].enabled = true;
                        broadcastNewTracks(myStream, 'audio');
                    }
                    clearTimeout(StreamAdmin);
                }
            });

            socket.on('screen sharing off', (data) => {
                console.log('screen close');
                if (data.socketId !== socketId) {
                    screenSharing = true;
                    document.getElementById('share-screen').disabled = false;
                }
            });

            socket.on('screen sharing on', (data) => {
                console.log('screen on');
                if (data.socketId !== socketId) {
                    screenSharing = false;
                    document.getElementById('share-screen').disabled = true;
                }
            });


            socket.on('newUserStart', (data) => {
                pc.push(data.sender);
                init(false, data.sender, data.username);
            });


            socket.on('ice candidates', async (data) => {
                data.candidate ? await pc[data.sender].addIceCandidate(new RTCIceCandidate(data.candidate)) : '';
            });


            socket.on('sdp', async (data) => {
                if (data.description.type === 'offer') {
                    data.description ? await pc[data.sender].setRemoteDescription(new RTCSessionDescription(data.description)) : '';

                    let answer = await pc[data.sender].createAnswer();

                    h.getUserFullMedia().then(async (stream) => {
                        if (!document.getElementById('local').srcObject) {
                            h.setLocalStream(stream);
                        }

//                        answer.sdp = h.updateBandwidthRestriction(answer.sdp, 125);

                        await pc[data.sender].setLocalDescription(answer);

                        socket.emit('sdp', {description: pc[data.sender].localDescription, to: data.sender, sender: socketId});


                        //save my stream
                        myStream = stream;

                        stream.getTracks().forEach((track) => {
                            pc[data.sender].addTrack(track, stream);
                        });

                    }).catch((e) => {
                        console.error(e);
                    });
                } else if (data.description.type === 'answer') {
                    await pc[data.sender].setRemoteDescription(new RTCSessionDescription(data.description));
                }
            });


            socket.on('chat', (data) => {
                h.addChat(data, 'remote');
            })
        });


        function getAndSetUserStream() {
            h.getUserFullMedia().then((stream) => {
                //save my stream
                myStream = stream;

                h.setLocalStream(stream);
            }).catch((e) => {
                console.error(`stream error: ${e}`);
            });
        }


        function sendMsg(msg) {
            let data = {
                room: room,
                msg: msg,
                sender: username
            };

            //emit chat message
            socket.emit('chat', data);


            //add localchat
            h.addChat(data, 'local');
        }



        function init(createOffer, partnerName, username = false) {
            pc[partnerName] = new RTCPeerConnection(h.getIceServer());

            if (screen && screen.getTracks().length) {
                screen.getTracks().forEach((track) => {
                    pc[partnerName].addTrack(track, screen);//should trigger negotiationneeded event
                });
            } else if (myStream) {
                myStream.getTracks().forEach((track) => {
                    pc[partnerName].addTrack(track, myStream);//should trigger negotiationneeded event
                });
            } else {
                h.getUserFullMedia().then((stream) => {
                    //save my stream
                    myStream = stream;

                    stream.getTracks().forEach((track) => {
                        pc[partnerName].addTrack(track, stream);//should trigger negotiationneeded event
                    });

                    h.setLocalStream(stream);
                }).catch((e) => {
                    console.error(`stream error: ${e}`);
                });
            }



            //create offer
            if (createOffer) {
                pc[partnerName].onnegotiationneeded = async () => {
                    let offer = await pc[partnerName].createOffer();
                    offer.sdp = h.updateBandwidthRestriction(offer.sdp, 125);
                    await pc[partnerName].setLocalDescription(offer);

                    socket.emit('sdp', {description: pc[partnerName].localDescription, to: partnerName, sender: socketId});
                };
            }



            //send ice candidate to partnerNames
            pc[partnerName].onicecandidate = ({candidate}) => {
                socket.emit('ice candidates', {candidate: candidate, to: partnerName, sender: socketId});
            };



            //add
            pc[partnerName].ontrack = (e) => {
                let str = e.streams[0];
                if (document.getElementById(`${partnerName}-video`)) {
                    document.getElementById(`${partnerName}-video`).srcObject = str;
                } else {
                    //video elem
                    let newVid = document.createElement('video');
                    newVid.id = `${partnerName}-video`;
                    newVid.srcObject = str;
                    newVid.autoplay = true;
                    newVid.className = 'remote-video';
                    newVid.style = '"width:100%; height:100%';
                    newVid.muted = h.audio;

                    //video controls elements
                    let controlDiv = document.createElement('div');
                    controlDiv.className = 'remote-video-controls';
                    controlDiv.innerHTML = `<i class="fa fa-microphone text-white pr-3 mute-remote-mic" title="Mute"></i>
                        <i class="fa fa-expand text-white expand-remote-video" title="Expand"></i>`;

                    //create a new div for card
                    let cardDiv = document.createElement('div');
                    cardDiv.className = 'card card-sm custom-card-video';
                    cardDiv.id = partnerName;
                    cardDiv.appendChild(newVid);
                    cardDiv.appendChild(controlDiv);
                    if (username) {
                        let controlDiv = document.createElement('div');
                        controlDiv.className = 'remote-video-names';
                        controlDiv.innerHTML = username;
                        cardDiv.appendChild(controlDiv);

                    }

                    //put div in main-section elem
                    document.getElementById('videos').appendChild(cardDiv);

                    h.adjustVideoElemSize();
                }
            };



            pc[partnerName].onconnectionstatechange = (d) => {
                switch (pc[partnerName].iceConnectionState) {
                    case 'disconnected':
                    case 'failed':
                        h.closeVideo(partnerName);
                        break;

                    case 'closed':
                        h.closeVideo(partnerName);
                        break;
                }
            };



            pc[partnerName].onsignalingstatechange = (d) => {
                switch (pc[partnerName].signalingState) {
                    case 'closed':
                        console.log("Signalling state is 'closed'");
                        h.closeVideo(partnerName);
                        break;
                }
            };
        }



        function shareScreen() {
            h.shareScreen().then((stream) => {
                h.toggleShareIcons(true);

                //disable the video toggle btns while sharing screen. This is to ensure clicking on the btn does not interfere with the screen sharing
                //It will be enabled was user stopped sharing screen
                if (video == '1' || owner == '1') {
                    h.toggleVideoBtnDisabled(true);
                }

                //save my screen stream
                screen = stream;

                //share the new stream with all partners
                broadcastNewTracks(stream, 'video', false);

                socket.emit('screen sharing on', {to: socketId, socketId: socketId, });

                //When the stop sharing button shown by the browser is clicked
                screen.getVideoTracks()[0].addEventListener('ended', () => {
                    stopSharingScreen();
                });
            }).catch((e) => {
                console.error(e);
            });
        }



        function stopSharingScreen() {
            //enable video toggle btn
            if (video == '1' || owner == '1') {
                h.toggleVideoBtnDisabled(false);
            }

            return new Promise((res, rej) => {
                screen.getTracks().length ? screen.getTracks().forEach(track => track.stop()) : '';

                res();
            }).then(() => {
                h.toggleShareIcons(false);
                if (video == '1' || owner == '1') {
                    broadcastNewTracks(myStream, 'video');
                }
            }).catch((e) => {
                console.error(e);
            });
        }



        function broadcastNewTracks(stream, type, mirrorMode = true) {
            h.setLocalStream(stream, mirrorMode);

            let track = type == 'audio' ? stream.getAudioTracks()[0] : stream.getVideoTracks()[0];

            for (let p in pc) {
                let pName = pc[p];

                if (typeof pc[pName] == 'object') {
                    h.replaceTrack(track, pc[pName]);
                }
        }
        }


        function toggleRecordingIcons(isRecording) {
            let e = document.getElementById('record');

            if (isRecording) {
                e.setAttribute('title', 'Stop recording');
                e.children[0].classList.add('text-danger');
                e.children[0].classList.remove('text-white');
            } else {
                e.setAttribute('title', 'Record');
                e.children[0].classList.add('text-white');
                e.children[0].classList.remove('text-danger');
            }
        }


        function startRecording(stream) {
            mediaRecorder = new MediaRecorder(stream, {
                mimeType: 'video/webm;codecs=H264'
            });

            mediaRecorder.start(1000);
            toggleRecordingIcons(true);

            mediaRecorder.ondataavailable = function (e) {
                recordedStream.push(e.data);
            }

            mediaRecorder.onstop = function () {
                toggleRecordingIcons(false);

                h.saveRecordedStream(recordedStream, username);

                setTimeout(() => {
                    recordedStream = [];
                }, 3000);
            }

            mediaRecorder.onerror = function (e) {
                console.error(e);
            }
        }


        //Chat textarea
        document.getElementById('chat-input').addEventListener('keypress', (e) => {
            if (e.which === 13 && (e.target.value.trim())) {
                e.preventDefault();

                sendMsg(e.target.value);

                setTimeout(() => {
                    e.target.value = '';
                }, 50);
            }
        });

        if (video == '1' || owner == '1') {
            //When the video icon is clicked
            document.getElementById('toggle-video').addEventListener('click', (e) => {
                e.preventDefault();

                let elem = document.getElementById('toggle-video');

                if (myStream.getVideoTracks()[0].enabled) {
                    e.target.classList.remove('fa-video');
                    e.target.classList.add('fa-video-slash');
                    elem.setAttribute('title', 'Show Video');

                    myStream.getVideoTracks()[0].enabled = false;
                } else {
                    e.target.classList.remove('fa-video-slash');
                    e.target.classList.add('fa-video');
                    elem.setAttribute('title', 'Hide Video');

                    myStream.getVideoTracks()[0].enabled = true;
                }

                broadcastNewTracks(myStream, 'video');
            });
        }


        //When the mute icon is clicked
        document.getElementById('toggle-mute').addEventListener('click', (e) => {
            e.preventDefault();

            let elem = document.getElementById('toggle-mute');

            if (myStream.getAudioTracks()[0].enabled) {
                e.target.classList.remove('fa-microphone-alt');
                e.target.classList.add('fa-microphone-alt-slash');
                elem.setAttribute('title', 'Unmute');

                myStream.getAudioTracks()[0].enabled = false;
            } else {
                e.target.classList.remove('fa-microphone-alt-slash');
                e.target.classList.add('fa-microphone-alt');
                elem.setAttribute('title', 'Mute');

                myStream.getAudioTracks()[0].enabled = true;
            }

            broadcastNewTracks(myStream, 'audio');
        });


        //When user clicks the 'Share screen' button
        if (screen_share == '1' || owner == '1') {
            document.getElementById('share-screen').addEventListener('click', (e) => {
                e.preventDefault();

                if (screen && screen.getVideoTracks().length && screen.getVideoTracks()[0].readyState != 'ended') {
                    stopSharingScreen();
                    socket.emit('screen sharing off', {to: socketId, socketId: socketId});
                } else {
                    if (screenSharing) {
                        console.log('screen emit on');
                        shareScreen();
                    } else {
                        alert('Some one already using screen sharing');
                    }
                }
            });
        }

        //When record button is clicked
        document.getElementById('record').addEventListener('click', (e) => {
            /**
             * Ask user what they want to record.
             * Get the stream based on selection and start recording
             */
            if (!mediaRecorder || mediaRecorder.state == 'inactive') {
                h.toggleModal('recording-options-modal', true);
            } else if (mediaRecorder.state == 'paused') {
                mediaRecorder.resume();
            } else if (mediaRecorder.state == 'recording') {
                var ask = window.confirm("Are you sure to Stop and Save Recording?");
                if (ask) {
                    mediaRecorder.stop();
                }
            }
        });


        //When user choose to record screen
        document.getElementById('record-screen').addEventListener('click', () => {
            h.toggleModal('recording-options-modal', false);

            if (screen && screen.getVideoTracks().length) {
                startRecording(screen);
            } else {
                h.shareScreen().then((screenStream) => {
                    startRecording(screenStream);
                }).catch(() => {
                });
            }
        });


        //When user choose to record own video
        document.getElementById('record-video').addEventListener('click', () => {
            h.toggleModal('recording-options-modal', false);

            if (myStream && myStream.getTracks().length) {
                startRecording(myStream);
            } else {
                h.getUserFullMedia().then((videoStream) => {
                    startRecording(videoStream);
                }).catch(() => {
                });
            }
        });
        //When user exit
        document.getElementById('leave-room').addEventListener('click', () => {
            if (mediaRecorder.state == 'recording') {
                var ask = window.confirm("Recorind is running, would you want to save it?");
                if (ask) {
                    closePromt = false;
                    mediaRecorder.stop();
                }
                setTimeout(function () {
                    window.location.href = "/dashboard";
                }, 3000);
            } else {
                var ask = window.confirm("Are you sure to exit from this meeting?");
                if (ask) {
                    closePromt = false;
                    window.location.href = "/dashboard";
                }
            }
        });


        window.addEventListener('beforeunload', function (e) {
            if (closePromt) {
                return 'Are you sure you want to close this meeting.';
            }
        });

        document.querySelector('#file-upload').addEventListener('change', function () {
            // user has not chosen any file
            if (document.querySelector('#file-upload').files.length == 0) {
                alert('Error : No file selected');
                return;
            }

            // first file that was chosen
            var file = document.querySelector('#file-upload').files[0];

            // allowed types
            var mime_types = ['image/jpeg', 'image/png', 'image/gif', 'application/msword', 'application/rtf', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet'];

            // validate MIME type
            if (mime_types.indexOf(file.type) == -1) {
                alert('Error : Incorrect file type');
                return;
            }

            // max 2 MB size allowed
            if (file.size > 2 * 1024 * 1024) {
                alert('Error : Exceeded size 2MB');
                return;
            }


            var fileEl = document.getElementById('file-upload');
            var uploadIds = uploader.upload(fileEl, {
                data: {/* Arbitrary data... */}
            });
        });
    }
});