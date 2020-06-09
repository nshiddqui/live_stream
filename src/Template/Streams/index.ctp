<?= $this->Html->component('fancybox/jquery.fancybox.css?v=2.1.5') ?>
<?= $this->Html->component('fancybox/jquery.fancybox.js?v=2.1.5', 'script') ?>
<div class="row">
    <div class="col-12">

    </div>
    <div class="col-lg-12 col-md-12 col-sm-12">
        <!-- collapse able Card -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardJoin" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Upcoming Meetings</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse border-bottom-primary show" id="collapseCardJoin" style="">
                <div class="card-body table-responsive">
                    <?= $this->DataTables->render('StreamDetails') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display: none;" id="send-message" class="modal fade" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inform To All</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <!-- Start Form Element -->
                <?= $this->Form->create(null, ['url' => ['controller' => 'streams', 'action' => 'send']]) ?>
                <div class="d-none">
                    <?= $this->Form->control('id', ['id' => 'meeting-id']) ?>
                </div>
                <!-- Title for stream -->
                <?= $this->Form->control('message', ['type' => 'textarea', 'required' => true]) ?>
                <hr>
                <!-- Trigger to start stream -->
                <?= $this->Form->button('Send') ?>
                <!-- End Form Element -->
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<script>
// Set custom style, close if clicked, change title type and overlay color
    $(document).ready(function () {
        $('#send-message').on('shown.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var form_url = button.data('val');
            $('#meeting-id').val(form_url);
        })
    });
</script>