<?php

//use Cake\Utility\Security;
//use Cake\Core\Configure;

foreach ($results as $result) {
    $secureId = base64_encode($result->stream_id);
    if ($Auth->user('id') === $result['stream']['user']->id) {
        $text = 'Start';
        $allow = true;
    } else if ($result['stream']->is_active == '1') {
        $text = 'Join';
        $allow = false;
    } else {
        $text = false;
        $allow = false;
    }


    $this->DataTables->prepareData([
        $result['stream']->title,
        $result['stream']->start_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        $result['stream']->end_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        h($result['stream']['user']->name),
        ($text ? $this->Html->link($text, ['controller' => 'stream', 'action' => 'index', $secureId]) : 'Not Started' ) .
        ($allow ? $this->Html->link('<i class="btn-primary btn-sm fas fa-pencil-alt mx-1"></i>', ['action' => 'edit', $result->id], ['escape' => false]) .
                $this->Form->postLink('<i class="btn-danger btn-sm fas fa-trash mx-1"></i>', ['action' => 'delete', $result->id], ['confirm' => __('Are you sure you want to delete # {0}?', $result['stream']->title), 'escape' => false]) : '')
    ]);
}
echo $this->DataTables->response();
