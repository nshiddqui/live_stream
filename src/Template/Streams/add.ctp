<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stream $stream
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Streams'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Stream Details'), ['controller' => 'StreamDetails', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Stream Detail'), ['controller' => 'StreamDetails', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="streams form large-9 medium-8 columns content">
    <?= $this->Form->create($stream) ?>
    <fieldset>
        <legend><?= __('Add Stream') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('start_time');
            echo $this->Form->control('end_time');
            echo $this->Form->control('user_id', ['options' => $users]);
            echo $this->Form->control('request_token');
            echo $this->Form->control('verify_token');
            echo $this->Form->control('room_token');
            echo $this->Form->control('broadcaster');
            echo $this->Form->control('is_active');
            echo $this->Form->control('ip_address');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
