<?php
$myTemplates = [
    'input' => '<input class="form-control form-control-user" type="{{type}}" name="{{name}}" {{attrs}}>',
];
$this->Form->setTemplates($myTemplates);
?>
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Continue with us!</h1>
            </div>
            <div class="text-center">
                <?= $this->Flash->render() ?>
            </div>
            <!-- Login Form Start Here -->
            <?= $this->Form->create(null, ['class' => 'user']) ?>
            <!-- Login Email ID -->
            <?= $this->Form->control('username', ['label' => false, 'required' => true, 'placeholder' => 'Enter Username']) ?>
            <!-- Login Password -->
            <?= $this->Form->control('password', ['label' => false, 'required' => true, 'placeholder' => 'Password']) ?>
            <!-- Trigger to Login -->
            <?= $this->Form->button('Login', ['class' => 'btn btn-primary btn-user btn-block']) ?>
            <!-- End Form Element -->
            <?= $this->Form->end() ?>
            <hr>
            <div class="text-center">
                <?= $this->Html->link('Forgot Password?', '/forgot-password', ['class' => 'small']) ?>
            </div>
            <div class="text-center">
                <?= $this->Html->link('Create an Account!', '/register', ['class' => 'small']) ?>
            </div>
        </div>
    </div>
</div>
