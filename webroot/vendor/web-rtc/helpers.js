export default {
    generateRandomString() {
        return Math.random().toString(36).slice(2).substring(0, 15);
    },

    closeVideo(elemId) {
        if (document.getElementById(elemId)) {
            document.getElementById(elemId).remove();
            this.adjustVideoElemSize();
        }
    },

    pageHasFocus() {
        return !(document.hidden || document.onfocusout || window.onpagehide || window.onblur);
    },

    getQString(url = '', keyToReturn = '') {
        url = url ? url : location.href;
        let queryStrings = decodeURIComponent(url).split('#', 2)[0].split('?', 2)[1];

        if (queryStrings) {
            let splittedQStrings = queryStrings.split('&');

            if (splittedQStrings.length) {
                let queryStringObj = {};

                splittedQStrings.forEach(function (keyValuePair) {
                    let keyValue = keyValuePair.split('=', 2);

                    if (keyValue.length) {
                        queryStringObj[keyValue[0]] = keyValue[1];
                    }
                });

                return keyToReturn ? (queryStringObj[keyToReturn] ? queryStringObj[keyToReturn] : null) : queryStringObj;
            }

            return null;
        }

        return null;
    },

    userMediaAvailable() {
        return !!(navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
    },

    getUserFullMedia() {
        if (this.userMediaAvailable()) {
            return navigator.mediaDevices.getUserMedia({
                video: true,
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true
                }
            });
        } else {
            throw new Error('User media not available');
        }
    },

    getUserAudio() {
        if (this.userMediaAvailable()) {
            return navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true
                }
            });
        } else {
            throw new Error('User media not available');
        }
    },

    shareScreen() {
        if (this.userMediaAvailable()) {
            return navigator.mediaDevices.getDisplayMedia({
                video: {
                    cursor: "always"
                },
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100
                }
            });
        } else {
            throw new Error('User media not available');
        }
    },

    getIceServer() {
        // "turns:eu-turn4.xirsys.com:5349?transport=tcp"
        // "turns:eu-turn4.xirsys.com:443?transport=tcp"
        // "turn:eu-turn4.xirsys.com:80?transport=tcp",
        // "turn:eu-turn4.xirsys.com:3478?transport=udp",
        return {
            iceServers: [
//                {url: "stun:stun.l.google.com:19302"},
//                {url:"stun:stun1.l.google.com:19302"},
//                {url:"stun:stun2.l.google.com:19302"},
//                {url:"stun:stun3.l.google.com:19302"},
//                {url:"stun:stun4.l.google.com:19302"},
//                {url:"stun:stun01.sipphone.com"},
//                {url:"stun:stun.ekiga.net"},
//                {url:"stun:stun.fwdnet.net"},
//                {url:"stun:stun.ideasip.com"},
//                {url:"stun:stun.iptel.org"},
//                {url:"stun:stun.rixtelecom.se"},
//                {url:"stun:stun.schlund.de"},
//                {url:"stun:stunserver.org"},
//                {url:"stun:stun.softjoys.com"},
//                {url:"stun:stun.voiparound.com"},
//                {url:"stun:stun.voipbuster.com"},
//                {url:"stun:stun.voipstunt.com"},
//                {url:"stun:stun.voxgratia.org"},
//                {url:"stun:stun.xten.com"},
//                {
//                    username: "shelty",
//                    credential: "nazim@123",
//                    urls: [
//                        "turn:103.250.186.37:3478",
//                    ]
//                }
                {
                    "urls": [
                        "turn:13.250.13.83:3478?transport=udp"
                    ],
                    "username": "YzYNCouZM1mhqhmseWk6",
                    "credential": "YzYNCouZM1mhqhmseWk6"
                }
            ]
        };
    },

    addChat(data, senderType) {
        let chatMsgDiv = document.querySelector('#chat-messages');
        let contentAlign = 'justify-content-end';
        let senderName = 'You';
        let msgBg = 'bg-white';

        if (senderType === 'remote') {
            contentAlign = 'justify-content-start';
            senderName = data.sender;
            msgBg = '';

            this.toggleChatNotificationBadge();
        }

        let infoDiv = document.createElement('div');
        infoDiv.className = 'sender-info';
        infoDiv.innerHTML = `${senderName} - ${moment().format('Do MMMM, YYYY h:mm a')}`;

        let colDiv = document.createElement('div');
        colDiv.className = `col-10 card chat-card msg ${msgBg}`;
        colDiv.innerHTML = data.msg;

        let rowDiv = document.createElement('div');
        rowDiv.className = `row ${contentAlign} mb-2`;


        colDiv.appendChild(infoDiv);
        rowDiv.appendChild(colDiv);

        chatMsgDiv.appendChild(rowDiv);

        /**
         * Move focus to the newly added message but only if:
         * 1. Page has focus
         * 2. User has not moved scrollbar upward. This is to prevent moving the scroll position if user is reading previous messages.
         */
        if (this.pageHasFocus) {
            rowDiv.scrollIntoView();
        }
    },

    toggleChatNotificationBadge() {
        if (document.querySelector('#chat-pane').classList.contains('chat-opened')) {
            document.querySelector('#new-chat-notification').setAttribute('hidden', true);
        } else {
            document.querySelector('#new-chat-notification').removeAttribute('hidden');
        }
    },

    replaceTrack(stream, recipientPeer) {
        let sender = recipientPeer.getSenders ? recipientPeer.getSenders().find(s => s.track && s.track.kind === stream.kind) : false;

        sender ? sender.replaceTrack(stream) : '';
    },

    toggleShareIcons(share) {
        let shareIconElem = document.querySelector('#share-screen');

        if (share) {
            shareIconElem.setAttribute('title', 'Stop sharing screen');
            shareIconElem.children[0].classList.add('text-danger');
            shareIconElem.children[0].classList.remove('text-primary');
        } else {
            shareIconElem.setAttribute('title', 'Share screen');
            shareIconElem.children[0].classList.add('text-primary');
            shareIconElem.children[0].classList.remove('text-danger');
        }
    },

    toggleVideoBtnDisabled(disabled) {
        document.getElementById('toggle-video').disabled = disabled;
    },

    maximiseStream(e) {
        let elem = e.target.parentElement.previousElementSibling;

        elem.requestFullscreen() || elem.mozRequestFullScreen() || elem.webkitRequestFullscreen() || elem.msRequestFullscreen();
        // document.querySelector('#close-single-peer-btn').style.display = 'block';

        // e.target.parentElement.previousElementSibling.classList.remove('remote-video');
        // e.target.parentElement.previousElementSibling.classList.add('single-peer-video');

        // //hide the other elements
        // let remoteVideoElems = document.getElementsByClassName('remote-video');

        // if(remoteVideoElems.length){
        //     for(let i = 0; i < remoteVideoElems.length; i++){
        //         remoteVideoElems[i].style.display = 'none';
        //     }
        // }
    },

    singleStreamToggleMute(e) {
        if (e.target.classList.contains('fa-microphone')) {
            e.target.parentElement.previousElementSibling.muted = true;
            e.target.classList.add('fa-microphone-slash');
            e.target.classList.remove('fa-microphone');
        } else {
            e.target.parentElement.previousElementSibling.muted = false;
            e.target.classList.add('fa-microphone');
            e.target.classList.remove('fa-microphone-slash');
        }
    },

    saveRecordedStream(stream, user) {
        let blob = new Blob(stream, {type: 'video/mp4'});

        let file = new File([blob], `${user}-${moment().unix()}-record.mp4`);

        saveAs(file);
    },

    toggleModal(id, show) {
        let el = document.getElementById(id);

        if (show) {
            el.style.display = 'block';
            el.removeAttribute('aria-hidden');
        } else {
            el.style.display = 'none';
            el.setAttribute('aria-hidden', true);
        }
    },

    setLocalStream(stream, mirrorMode = true) {
        const localVidElem = document.getElementById('local');

        localVidElem.srcObject = stream;
        mirrorMode ? localVidElem.classList.add('mirror-mode') : localVidElem.classList.remove('mirror-mode');
    },

    adjustVideoElemSize() {
        let elem = document.getElementsByClassName('card');
        let totalRemoteVideosDesktop = elem.length;
        let newWidth = totalRemoteVideosDesktop <= 2 ? '50%' : (
                totalRemoteVideosDesktop == 3 ? '33.33%' : (
                        totalRemoteVideosDesktop <= 8 ? '25%' : (
                                totalRemoteVideosDesktop <= 15 ? '20%' : (
                                        totalRemoteVideosDesktop <= 18 ? '16%' : (
                                                totalRemoteVideosDesktop <= 23 ? '15%' : (
                                                        totalRemoteVideosDesktop <= 32 ? '12%' : '10%'
                                                        )
                                                )
                                        )
                                )
                        )
                );


        for (let i = 0; i < totalRemoteVideosDesktop; i++) {
            elem[i].style.width = newWidth;
            elem[i].style.height = newWidth;
        }
    },

    createDemoRemotes(str, total = 6) {
        let i = 0;

        let testInterval = setInterval(() => {
            let newVid = document.createElement('video');
            newVid.id = `demo-${i}-video`;
            newVid.srcObject = str;
            newVid.autoplay = true;
            newVid.className = 'remote-video';

            //video controls elements
            let controlDiv = document.createElement('div');
            controlDiv.className = 'remote-video-controls';
            controlDiv.innerHTML = `<i class="fa fa-microphone text-danger pr-3 mute-remote-mic" title="Mute"></i>
                <i class="fa fa-expand text-danger expand-remote-video" title="Expand"></i>`;

            //create a new div for card
            let cardDiv = document.createElement('div');
            cardDiv.className = 'card card-sm';
            cardDiv.id = `demo-${i}`;
            cardDiv.appendChild(newVid);
            cardDiv.appendChild(controlDiv);

            //put div in main-section elem
            document.getElementById('videos').appendChild(cardDiv);

            this.adjustVideoElemSize();

            i++;

            if (i == total) {
                clearInterval(testInterval);
            }
        }, 2000);
    }
};