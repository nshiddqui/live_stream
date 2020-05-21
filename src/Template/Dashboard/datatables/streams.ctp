<?php

//use Cake\Utility\Security;
//use Cake\Core\Configure;

foreach ($results as $result) {
    $secureId = base64_encode($result['stream_detail']->id);
//    $secureId = Security::encrypt($result['stream_detail']->id, Configure::read('SECURITY_KEY'));
    if ($Auth->user('id') === $result['user']->id) {
        $text = 'Start';
    } else {
        $text = 'Join';
    }


    $this->DataTables->prepareData([
        $result->title,
        $result->start_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        $result->end_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        h($result['user']->name),
        ($text) ? $this->Html->link($text, ['controller' => 'stream', 'action' => 'index', $secureId]) : 'Not Started'
    ]);
}
echo $this->DataTables->response();
