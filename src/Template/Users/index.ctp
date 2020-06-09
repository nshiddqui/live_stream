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
                    <?= $this->Form->create($user, ['type' => 'file']) ?>
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <fieldset>
                                <?php
                                echo $this->Form->control('name');
                                echo $this->Form->control('username', ['disabled' => true]);
                                echo $this->Form->control('mobile_number', ['disabled' => true]);
                                echo $this->Form->control('email', ['disabled' => true]);
                                echo $this->Form->control('profile_image', ['type' => 'file', 'class' => 'd-none', 'onchange' => "loadFile(event,'profile-image-file')", 'label' => false]);
                                echo $this->Form->control('password', ['value' => '', 'required' => false, 'onchange' => 'checkPassword(this.value)', 'placeholder' => 'Password']);
                                echo $this->Form->control('confirm_password', ['value' => '', 'type' => 'password', 'required' => false, 'placeholder' => 'Confirm Password']);
                                ?>
                            </fieldset>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <label for="profile-image" class="rounded-circle col-12">
                                <?= $this->Html->image('profile_image/' . $user->profile_image, ['class' => 'col-12', 'id' => 'profile-image-file']) ?>
                            </label>
                        </div>
                    </div>
                    <?= $this->Form->button(__('Update')) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function checkPassword(value) {
        if (value.length !== 0) {
            $('#confirm-password').prop('required', true);
        } else {
            $('#confirm-password').prop('required', false);
        }
    }
    var loadFile = function (event, id) {
        var image = document.getElementById(id);
        image.src = URL.createObjectURL(event.target.files[0]);
    };
</script>