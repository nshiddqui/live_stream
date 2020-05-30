<?= $this->Html->component('web-rtc/app') ?>
<?= $this->Html->css('https://use.fontawesome.com/releases/v5.7.2/css/all.css') ?>
<script>
    const room = '<?= $stream_data['stream']['request_token'] ?>';
    const username = '<?= $current_user['username'] ?>';
</script>
<?= $this->Html->component('web-rtc/socket.io', 'script') ?>
<?= $this->Html->component('web-rtc/rtc', 'script', ['type' => 'module']) ?>
<?= $this->Html->component('web-rtc/events', 'script', ['type' => 'module']) ?>
<?= $this->Html->component('web-rtc/adapter.min', 'script') ?>
<?= $this->Html->component('web-rtc/FileSaver.min', 'script') ?>

<div class="custom-modal" id='recording-options-modal'>
    <div class="custom-modal-content">
        <div class="row text-center">
            <div class="col-md-6 mb-2">
                <span class="record-option" id='record-video'>Record video</span>
            </div>
            <div class="col-md-6 mb-2">
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


<div class="pull-right room-comm stream-setting" hidden>
    <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-video' title="Hide Video">
        <i class="fa fa-2x fa-video text-primary"></i>
    </button>

    <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-mute' title="Mute">
        <i class="fa fa-2x fa-microphone text-primary"></i>
    </button>

    <button class="btn btn-sm rounded-0 btn-no-effect" id='share-screen' title="Share screen">
        <i class="fa fa-2x fa-desktop text-primary"></i>
    </button>

    <button class="btn btn-sm rounded-0 btn-no-effect" id='record' title="Record">
        <i class="fa fa-2x fa-dot-circle text-primary"></i>
    </button>

    <button class="btn btn-sm text-primary pull-right btn-no-effect" id='toggle-chat-pane'>
        <span class="badge badge-danger very-small font-weight-lighter" id='new-chat-notification' hidden>New</span><i class="fa fa-2x fa-comment text-primary"></i>
    </button>

    <button class="btn btn-sm rounded-0 btn-no-effect text-primary">
        <a href="/dashboard" class="text-primary text-decoration-none"><i class="fa fa-2x fa-sign-out-alt text-primary" title="Leave"></i></a>
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

        <div class="col-md-3 chat-col d-print-none mb-2" style="background: #5a79d4;" id='chat-pane' hidden>
            <div class="row">
                <div class="col-12 text-center h2 mb-3" style="font-weight: 900;font-family: inherit;">Conversation</div>
            </div>
            <hr>
            <div id='chat-messages'></div>

            <div class="row">
                <textarea id='chat-input' class="form-control rounded-0 chat-box border-info" rows='3' placeholder="Type here..."></textarea>
            </div>
        </div>
    </div>
</div>