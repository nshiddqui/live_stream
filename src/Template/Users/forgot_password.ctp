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
                <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                <p class="mb-4">We get it, stuff happens. Just enter your email address below and we'll send you a link to reset your password!</p>
            </div>
            <div class="text-center">
                <?= $this->Flash->render() ?>
            </div>
            <!-- Forgot Password form start here -->
            <?= $this->Form->create(null, ['class' => 'user']) ?>
            <!-- Enter Email ID -->
            <?= $this->Form->control('email', ['label' => false, 'placeholder' => 'Enter Email Address...', 'required' => true]) ?>
            <!-- Trigger to Send Password -->
            <?= $this->Form->button('Reset Password', ['class' => 'btn btn-primary btn-user btn-block']) ?>
            <!-- End Form Element -->
            <?= $this->Form->end() ?>
            <hr>
            <div class="text-center">
                <?= $this->Html->link('Create an Account!', '/register', ['class' => 'small']) ?>
            </div>
            <div class="text-center">
                <?= $this->Html->link('Already have an account? Login!', '/', ['class' => 'small']) ?>
            </div>
        </div>
    </div>
</div>