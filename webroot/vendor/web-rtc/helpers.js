export default {
    audio: false,
    generateRandomString() {
        return Math.random().toString(36).slice(2).substring(0, 15);
    },
    updateBandwidthRestriction(sdp, bandwidth) {
        let modifier = 'AS';
        if (adapter.browserDetails.browser === 'firefox') {
            bandwidth = (bandwidth >>> 0) * 1000;
            modifier = 'TIAS';
        }
        if (sdp.indexOf('b=' + modifier + ':') === -1) {
            // insert b= after c= line.
            sdp = sdp.replace(/c=IN (.*)\r\n/, 'c=IN $1\r\nb=' + modifier + ':' + bandwidth + '\r\n');
        } else {
            sdp = sdp.replace(new RegExp('b=' + modifier + ':.*\r\n'), 'b=' + modifier + ':' + bandwidth + '\r\n');
        }
        return sdp;
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
                video: (video == '1' || owner == '1' ? {height: {ideal: 144, max: 240}, frameRate: {ideal: 10, max: 15}, quality: 10} : false),
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
        return {
            iceServers: [
                { 
                    username: "nazim",  
                    credential: "12345",
                    urls: [    
                           "turn:103.205.143.42:5349"
                      ]
                }
            ],
            iceTransportPolicy: 'relay'
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

    remoteStreamToggleMute(e) {
        let toggleMute = document.getElementById('toggle-mute-all');
        let elem = document.getElementsByClassName('mute-remote-mic');
        let totalRemoteVideosDesktop = elem.length;
        if (e.target.classList.contains('fa-microphone')) {
            e.target.classList.remove('fa-microphone');
            e.target.classList.add('fa-microphone-slash');
            toggleMute.setAttribute('title', 'Unmute All');
            this.audio = true;
        } else {
            e.target.classList.add('fa-microphone');
            e.target.classList.remove('fa-microphone-slash');
            toggleMute.setAttribute('title', 'Mute All');
            this.audio = false;
        }
        for (let i = 0; i < totalRemoteVideosDesktop; i++) {
            if (this.audio) {
                elem[i].parentElement.previousElementSibling.muted = true;
                elem[i].classList.add('fa-microphone-slash');
                elem[i].classList.remove('fa-microphone');
            } else {
                elem[i].parentElement.previousElementSibling.muted = false;
                elem[i].classList.add('fa-microphone');
                elem[i].classList.remove('fa-microphone-slash');
            }
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
        let elem = document.getElementsByClassName('custom-card-video');
        let totalRemoteVideosDesktop = elem.length;
        let newWidth = totalRemoteVideosDesktop <= 2 ? '50%' : (
                totalRemoteVideosDesktop == 3 ? '33.33%' : '25%'
                );
        var w = window.innerWidth
                || document.documentElement.clientWidth
                || document.body.clientWidth;
        var newHeight;
        if (newWidth == '50%') {
            newHeight = window.outerHeight / 2 + 'px';
        } else if (newWidth == '33.33%') {
            newHeight = window.outerHeight / 3 + 'px';
        } else {
            newHeight = window.outerHeight / 4 + 'px';
        }
        if (w < 767) {
            newWidth = '100%';
            newHeight = window.outerHeight / 2 + 'px';
        }

        for (let i = 0; i < totalRemoteVideosDesktop; i++) {
            elem[i].style.width = newWidth;
            elem[i].style.height = newHeight;
            elem[i].querySelector('video').style.width = '100%';
            elem[i].querySelector('video').style.height = newHeight;
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
    },
    pauseStream() {
        document.getElementsByClassName('modal-backdrop')[0].style.zIndex = '99999999';
        document.getElementById('waiting-dialog').style.zIndex = '99999999999999';
    },
    continueStream() {
        document.getElementsByClassName('modal-backdrop')[0].style.zIndex = '1050';
        document.getElementById('waiting-dialog').style.zIndex = '1050';
    }
};