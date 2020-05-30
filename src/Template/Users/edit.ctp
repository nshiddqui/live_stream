<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Edit Profile') ?></legend>
        <?php
        echo $this->Form->control('name');
        echo $this->Form->control('email');
        echo $this->Form->control('password', ['value' => '', 'placeholder' => 'New Password']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
