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
        (($text) ? $this->Html->link('<i class="fas fa-video mx-2"></i>', ['controller' => 'stream', 'action' => 'index', $secureId], ['escape' => false]) : '<i class="fas fa-video mx-2"></i>') .
        ((count((array)$result['stream']['notification'])) ? $this->Html->link('<i class="btn-success btn-sm fas fa-bell mx-1"></i>', 'javascript:;', ['data-target' => '#send-message', 'data-toggle' => 'modal', 'data-url' => $this->Url->build(['controller' => 'Streams', 'action' => 'getMessage', $secureId]), 'escape' => false]) : '')
    ]);
}
echo $this->DataTables->response();
