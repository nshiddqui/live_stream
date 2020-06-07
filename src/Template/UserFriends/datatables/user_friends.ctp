<?php

use Cake\Utility\Security;
use Cake\Core\Configure;

foreach ($results as $result) {
    $secureId = Security::encrypt($result->id, Configure::read('SECURITY_KEY'));
    $this->DataTables->prepareData([
        $result['friend']->name,
        $result['friend']->email,
        $result->group,
        $this->Html->link('<i class="btn-primary btn-sm fas fa-pencil-alt mx-1"></i>', ['action' => 'edit', $result->id], ['escape' => false]) .
        $this->Form->postLink('<i class="btn-danger btn-sm fas fa-trash mx-1"></i>', ['action' => 'delete', $secureId], ['escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $result['friend']->email)])
    ]);
}
echo $this->DataTables->response();
