<div class="row">
    <div class="col-12">

    </div>
    <div class="col-lg-12 col-md-12 col-sm-12">
        <!-- collapse able Card -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardJoin" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Previous Meetings</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse border-bottom-primary show" id="collapseCardJoin" style="">
                <div class="card-body table-responsive">
                    <?= $this->DataTables->render('PreviousStreamDetails') ?>
                </div>
            </div>
        </div>
    </div>
</div>
