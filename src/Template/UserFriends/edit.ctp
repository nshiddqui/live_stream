<?= $this->Html->component('font-awesome-4.7/css/font-awesome.min', 'css', ['block' => 'css']) ?>
<?= $this->Html->component('jquery-chosen/chosen.jquery', 'css', ['block' => 'css']) ?>
<?= $this->Html->component('jquery-chosen/chosen.jquery.min', 'script', ['block' => 'scriptBottom']) ?>
<?= $this->Html->component('bootstrap-datepicker/bootstrap-datetimepicker.min', 'css', ['block' => 'css']) ?>
<?= $this->Html->component('bootstrap-datepicker/bootstrap-datetimepicker.min', 'script', ['block' => 'scriptBottom']) ?>
<?= $this->Html->script('stream_edit', ['block' => 'scriptBottom']) ?>
<div class="col-lg-12 col-md-12 col-sm-12">
    <!-- collapse able Card -->
    <div class="card shadow mb-4">
        <!-- Card Header - Accordion -->
        <a href="#collapseCardScheduled" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
            <h6 class="m-0 font-weight-bold text-primary">Edit Friend</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse border-bottom-primary show" id="collapseCardScheduled" style="">
            <div class="card-body">
                <!-- Start Form Element -->
                <?= $this->Form->create($user_friend) ?>
                <?= $this->Form->control('friend.name', ['readonly' => true]) ?>
                <!-- Title for stream -->
                <?= $this->Form->control('friend.username', ['readonly' => true]) ?>
                <?= $this->Form->control('group') ?>
                <hr>
                <!-- Trigger to start stream -->
                <?= $this->Form->button('Update Friend') ?>
                <!-- End Form Element -->
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>