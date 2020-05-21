<style>
    #streamer-container{
        position: fixed;
        right: 10px;
        bottom: 10px;
    }
</style>
<section class="experiment">
<!--    <section>
        <span>
            Private ?? <a href="/video-conferencing/" target="_blank" title="Open this link in new tab. Then your conference room will be private!"><code><strong id="unique-token">#123456789</strong></code></a>
        </span>

        <input type="text" id="conference-name" placeholder="Conference Name" style="width: 50%;">
        <button id="setup-new-room" class="setup">Setup New Conference</button>
    </section>-->

    <!-- list of all available conferencing rooms -->
    <table style="width: 100%;" id="rooms-list"></table>

    <!-- local/remote videos container -->
    <div id="videos-container"></div>
    <div id="streamer-container"></div>
</section>
<?= $this->element('web_socket') ?>