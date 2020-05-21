<?php
$myTemplates = [
    'input' => '<input class="form-control form-control-user" type="{{type}}" name="{{name}}" {{attrs}}>',
];
$this->Form->setTemplates($myTemplates);
?>
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-2">Change Your Password?</h1>
                <p class="mb-4">We get it, stuff happens. Just enter your password below, and change your account password!</p>
            </div>
            <div class="text-center">
                <?= $this->Flash->render() ?>
            </div>
            <!-- Forgot Password form start here -->
            <?= $this->Form->create($userDetails, ['class' => 'user']) ?>
            <!-- Enter Password -->
            <?= $this->Form->control('password', ['label' => false, 'placeholder' => 'Password', 'required' => true]) ?>
            <!-- Enter Confirmed Password -->
            <?= $this->Form->control('confirm_password', ['label' => false, 'type' => 'password', 'required' => true, 'placeholder' => 'Confirm Password', 'required' => true]) ?>
            <!-- Trigger to Send Password -->
            <?= $this->Form->button('Change Password', ['class' => 'btn btn-primary btn-user btn-block']) ?>
            <!-- End Form Element -->
            <?= $this->Form->end() ?>
            <div class="card mb-3 mt-3 pt-2 pr-2">
                <ul>
                    <li>Password must be at least 8 characters in length.</li>
                    <li>Password must include at least one upper case letter.</li>
                    <li>Password must include at least one number.</li>
                    <li>Password must include at least one special character.</li>
                </ul>
            </div>
        </div>
    </div>
</div>