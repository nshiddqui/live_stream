<div class="row">
    <div class="col-12">
        <!-- collapse able Card -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardAdd" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Add Friends</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse border-bottom-primary show" id="collapseCardAdd" style="">
                <div class="card-body table-responsive">
                    <!-- Start Form Element -->
                    <?= $this->Form->create(null) ?>
                    <div class="col-8 d-inline-block">
                        <!-- Scheduled time for stream -->
                        <?= $this->Form->control('email') ?>
                    </div>
                    <div class="col-2 d-inline-block">
                        <!-- Scheduled time for stream -->
                        <?= $this->Form->control('group') ?>
                    </div>
                    <div class="col-2 d-inline-block">
                        <!-- Trigger to start stream -->
                        <?= $this->Form->button('Add Friend') ?>
                    </div>
                    <!-- End Form Element -->
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <!-- collapse able Card -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardList" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Your Friends List</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse border-bottom-primary show" id="collapseCardList" style="">
                <div class="card-body table-responsive">
                    <?= $this->DataTables->render('UserFriends') ?>
                </div>
            </div>
        </div>
    </div>
</div>