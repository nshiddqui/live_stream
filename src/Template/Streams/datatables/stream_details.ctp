<?php

//use Cake\Utility\Security;
//use Cake\Core\Configure;

foreach ($results as $result) {
    $secureId = base64_encode($result->stream_id);
    if ($Auth->user('id') === $result['stream']['user']->id) {
        $text = '<i class="fas fa-video mx-2"></i>';
        $allow = true;
    } else {
        $text = '<i class="fas fa-video mx-2"></i>';
        $allow = false;
    }


    $this->DataTables->prepareData([
        $result['stream']->title,
        $result['stream']->start_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        $result['stream']->end_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        h($result['stream']['user']->name),
        ($text ? $this->Html->link($text, ['controller' => 'stream', 'action' => 'index', $secureId], ['escape' => false]) : '<i class="fas fa-video mx-2"></i>' ) .
        ($allow ? $this->Html->link('<i class="btn-primary btn-sm fas fa-pencil-alt mx-1"></i>', ['action' => 'edit', $result->stream_id], ['escape' => false]) .
                $this->Form->postLink('<i class="btn-danger btn-sm fas fa-trash mx-1"></i>', ['action' => 'delete', $result->stream_id], ['confirm' => __('Are you sure you want to delete # {0}?', $result['stream']->title), 'escape' => false]) .
                $this->Html->link('<i class="btn-success btn-sm fas fa-sms mx-1"></i>', 'javascript:;', ['data-target' => '#send-message', 'data-toggle' => 'modal', 'data-val' => $secureId, 'escape' => false]) : $this->Html->link('<i class="btn-primary btn-sm fas fa-eye mx-1"></i>', ['action' => 'edit', $result->stream_id], ['escape' => false]) )
    ]);
}
echo $this->DataTables->response();
