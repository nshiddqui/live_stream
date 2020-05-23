<?php

//use Cake\Utility\Security;
//use Cake\Core\Configure;

foreach ($results as $result) {
    $secureId = base64_encode($result->stream_id);
    if ($Auth->user('id') === $result['stream']['user']->id) {
        $text = 'Start';
    } else {
        $text = 'Join';
    }


    $this->DataTables->prepareData([
        $result['stream']->title,
        $result['stream']->start_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        $result['stream']->end_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        h($result['stream']['user']->name),
        ($text) ? $this->Html->link($text, ['controller' => 'stream', 'action' => 'index', $secureId]) : 'Not Started'
    ]);
}
echo $this->DataTables->response();
