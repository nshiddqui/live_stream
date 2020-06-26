<?= $this->Html->component('web-rtc/app') ?>
<?= $this->Html->css('https://use.fontawesome.com/releases/v5.7.2/css/all.css') ?>
<?= $this->Html->script('https://unpkg.com/draggabilly@2/dist/draggabilly.pkgd.min.js') ?>
<?= $this->Html->script('waitng-modal') ?>
<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.0/jquery.nicescroll.min.js') ?>
<script>
    const room = '<?= $stream_data['stream']['request_token'] ?>';
    const username = '<?= $current_user['name'] ?>';
    const profile = '<?= isset($current_user['profile_image']) ? $current_user['profile_image'] : 'logo.png' ?>';
    const owner = '<?= $stream_data['stream']->user_id == $current_user['id'] ? '1' : '0' ?>';
    const video = '<?= $stream_data['stream']->video ?>';
    const screen_share = '<?= $stream_data['stream']->screen_share ?>';
    const is_mobile = '<?= $mobile_user ?>';
    const iceServer = JSON.parse('<?= $ice_server ?>');
    const devices = navigator.mediaDevices.enumerateDevices();

    $(document).ready(function () {
        $(".local-video").draggabilly({
            // options...
        });
        $("#chat-messages").niceScroll({
            horizrailenabled: false,
            cursorwidth: "7px"
        });
        $("#participant-list").niceScroll({
            horizrailenabled: false,
            cursorwidth: "7px"
        });
    });
    const serverUrl = 'https://socket.claymould.com:3000';
    function openNav() {
        document.getElementById("mySidenav").style.width = "300px";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
    }
</script>
<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js') ?>
<?= $this->Html->component('web-rtc/rtc', 'script', ['type' => 'module']) ?>
<?= $this->Html->component('web-rtc/events', 'script', ['type' => 'module']) ?>
<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/7.4.0/adapter.min.js') ?>
<?= $this->Html->component('web-rtc/FileSaver.min', 'script') ?>
<?= $this->Html->component('web-rtc/socket.io-file-client', 'script') ?>
<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <div class="row heading-nav-stream">
        <div class="col-12 text-center h4 text-white mb-2" style="font-weight: 900;font-family: inherit;">Participant List</div>
    </div>
    <!--<a href="#" class="heading-nav-stream"><h4 style="font-weight: 900;font-family: inherit;">Participant List</h4></a>-->
    <div id="participant-list">
    </div>
</div>

<div class="custom-modal" id='recording-options-modal'>
    <div class="custom-modal-content">
        <div class="row text-center">
            <div class="col-md-6 col-sm-6 mb-4">
                <span class="record-option" id='record-video'>Record video</span>
            </div>
            <div class="col-md-6 col-sm-6 mb-4">
                <span class="record-option" id='record-screen'>Record screen</span>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <button class="btn btn-outline-danger" id='closeModal'>Close</button>
            </div>
        </div>
    </div>
</div>


<div class="pull-right room-comm stream-setting" id="stream-setting" hidden>
    <?php if ($stream_data['stream']->video || $stream_data['stream']->user_id == $current_user['id']) { ?>
        <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-video' title="Hide Video">
            <i class="fa fa-2x fa-video text-primary"></i>
        </button>
        <?php if ($mobile_user) { ?>
            <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-camera' title="Change Camera">
                <i class="fa fa-2x fa-camera text-primary"></i>
            </button>
        <?php } ?>
    <?php } ?>
    <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-mute' title="Mute">
        <i class="fa fa-2x fa-microphone text-primary"></i>
    </button>

    <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-mute-all' title="Mute All">
        <i class="fa fa-2x fa-microphone text-danger"></i>
    </button>
    <?php if ($stream_data['stream']->screen_share || $stream_data['stream']->user_id == $current_user['id']) { ?>
        <button class="btn btn-sm rounded-0 btn-no-effect" id='share-screen' title="Share screen">
            <i class="fa fa-2x fa-desktop text-primary"></i>
        </button>
    <?php } ?>
    <?php if (!$mobile_user) { ?>
        <button class="btn btn-sm rounded-0 btn-no-effect" id='record' title="Record">
            <i class="fa fa-2x fa-dot-circle text-primary"></i>
        </button>
        <button class="btn btn-sm rounded-0 btn-no-effect" style="display: none;" id='resume-record' title="Pause Record">
            <i class="fa fa-2x fa-pause text-danger"></i>
        </button>
    <?php } ?>

    <button class="btn btn-sm text-primary pull-right btn-no-effect" id='toggle-chat-pane'>
        <span class="badge badge-danger very-small font-weight-lighter" id='new-chat-notification' hidden>New</span><i class="fa fa-2x fa-comment text-primary"></i>
    </button>

    <button class="btn btn-sm rounded-0 btn-no-effect" onclick="openNav()" title="Participant">
        <i class="fa fa-2x fa-users text-primary"></i>
    </button>

    <button class="btn btn-sm rounded-0 btn-no-effect text-primary">
        <a href="#" id="leave-room" class="text-primary text-decoration-none"><i class="fa fa-2x fa-sign-out-alt text-primary" title="Leave"></i></a>
    </button>
</div>

<div class="container-fluid" id='room-create' hidden>
    <div class="row">
        <div class="col-12 h2 mt-5 text-center">Create Room</div>
    </div>

    <div class="row mt-2">
        <div class="col-12 text-center">
            <span class="form-text small text-danger" id='err-msg'></span>
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <label for="room-name">Room Name</label>
            <input type="text" id='room-name' class="form-control rounded-0" placeholder="Room Name">
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <label for="your-name">Your Name</label>
            <input type="text" id='your-name' class="form-control rounded-0" placeholder="Your Name">
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <button id='create-room' class="btn btn-block rounded-0 btn-info">Create Room</button>
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3" id='room-created'></div>
    </div>
</div>



<div class="container-fluid" id='username-set' hidden>
    <div class="row">
        <div class="col-12 h4 mt-5 text-center">Your Name</div>
    </div>

    <div class="row mt-2">
        <div class="col-12 text-center">
            <span class="form-text small text-danger" id='err-msg-username'></span>
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <label for="username">Your Name</label>
            <input type="text" id='username' class="form-control rounded-0" placeholder="Your Name">
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <button id='enter-room' class="btn btn-block rounded-0 btn-info">Enter Room</button>
        </div>
    </div>
</div>



<div class="container-fluid room-comm" hidden>
    <div class="row">
        <video class="local-video mirror-mode" id='local' volume='0' autoplay muted></video>
    </div>

    <div class="row">
        <div class="col-md-12 main" id='main-section'>                    
            <div class="row mt-2 mb-2" id='videos'></div>
        </div>

        <div class="col-md-3 chat-col d-print-none mb-2" style="background: #d3d3d3;" id='chat-pane' hidden>
            <div class="row heading-nav-stream">
                <div class="col-12 text-center h2 mb-3" style="font-weight: 900;font-family: inherit;">Conversation</div>
                <label for="file-upload"><i class="fa fa-file-alt custom-file-upload"></i></label>
                <input type="file" id="file-upload" style="display:none"> 
            </div>
            <div id='chat-messages'></div>

            <div class="row">
                <textarea id='chat-input' class="form-control rounded-0 chat-box border-info" rows='3' placeholder="Type here..."></textarea>
            </div>
        </div>
    </div>
</div>