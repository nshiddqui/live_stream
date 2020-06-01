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
            <h6 class="m-0 font-weight-bold text-primary">Edit Meeting</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse border-bottom-primary show" id="collapseCardScheduled" style="">
            <div class="card-body">
                <!-- Start Form Element -->
                <?= $this->Form->create($stream) ?>
                <!-- Title for stream -->
                <?= $this->Form->control('title') ?>
                <!-- Scheduled time for stream -->
                <?= $this->Form->control('start_time', ['type' => 'text']) ?>
                <!-- End time for stream -->
                <?= $this->Form->control('end_time', ['type' => 'text']) ?>
                <!-- List of email's for stream -->
                <?= $this->Form->label('emails') ?>
                <?= $this->Form->select('emails', $emails, ['data-placeholder' => 'Please select...', 'class' => 'group-result chosen-search-input default', 'multiple' => true, 'value' => $selected_users]) ?>
                <hr>
                <!-- Trigger to start stream -->
                <?= $this->Form->button('Update Stream') ?>
                <!-- End Form Element -->
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>