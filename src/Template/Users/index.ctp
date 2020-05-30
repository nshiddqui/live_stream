<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <!-- collapse able Card -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardJoin" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Edit Profile</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse border-bottom-primary show" id="collapseCardJoin" style="">
                <div class="card-body table-responsive">
                    <?= $this->Form->create($user) ?>
                    <fieldset>
                        <?php
                        echo $this->Form->control('name');
                        echo $this->Form->control('email', ['disabled' => true]);
                        echo $this->Form->control('password', ['value' => '', 'placeholder' => 'New Password', 'required' => false]);
                        ?>
                    </fieldset>
                    <?= $this->Form->button(__('Update')) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>