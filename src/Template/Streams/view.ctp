<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stream $stream
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Stream'), ['action' => 'edit', $stream->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Stream'), ['action' => 'delete', $stream->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stream->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Streams'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Stream'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Stream Details'), ['controller' => 'StreamDetails', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Stream Detail'), ['controller' => 'StreamDetails', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="streams view large-9 medium-8 columns content">
    <h3><?= h($stream->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Stream Detail') ?></th>
            <td><?= $stream->has('stream_detail') ? $this->Html->link($stream->stream_detail->id, ['controller' => 'StreamDetails', 'action' => 'view', $stream->stream_detail->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($stream->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $stream->has('user') ? $this->Html->link($stream->user->username, ['controller' => 'Users', 'action' => 'view', $stream->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Request Token') ?></th>
            <td><?= h($stream->request_token) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Verify Token') ?></th>
            <td><?= h($stream->verify_token) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Room Token') ?></th>
            <td><?= h($stream->room_token) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Broadcaster') ?></th>
            <td><?= h($stream->broadcaster) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Active') ?></th>
            <td><?= h($stream->is_active) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ip Address') ?></th>
            <td><?= h($stream->ip_address) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Start Time') ?></th>
            <td><?= h($stream->start_time) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('End Time') ?></th>
            <td><?= h($stream->end_time) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($stream->created) ?></td>
        </tr>
    </table>
</div>
