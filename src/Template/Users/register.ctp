<?php
$myTemplates = [
    'input' => '<input class="form-control form-control-user" type="{{type}}" name="{{name}}" {{attrs}}>',
    'inputContainer' => '{{content}}',
];
$this->Form->setTemplates($myTemplates);
?>
<div class="row">
    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
    <div class="col-lg-7">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
            </div>
            <div class="text-center">
                <?= $this->Flash->render() ?>
            </div>
            <?= $this->Form->create($user, ['class' => 'user']) ?>
            <div class="form-group">
                <?= $this->Form->control('name', ['label' => false, 'placeholder' => 'Full Name']) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('username', ['label' => false, 'placeholder' => 'Username']) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('email', ['label' => false, 'placeholder' => 'Email Address']) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('mobile_number', ['label' => false, 'type' => 'number', 'placeholder' => 'Mobile Number']) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('password', ['label' => false, 'placeholder' => 'Password']) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('confirm_password', ['type' => 'password', 'required' => true, 'label' => false, 'placeholder' => 'Confirm Password']) ?>
            </div>
            <!-- Trigger to Login -->
            <?= $this->Form->button('Register Account', ['class' => 'btn btn-primary btn-user btn-block']) ?>
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
            <hr>
            <div class="text-center">
                <?= $this->Html->link('Already have an account? Login!', '/', ['class' => 'small']) ?>
            </div>
        </div>
    </div>
</div>