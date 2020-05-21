<?php

use Cake\Utility\Security;
use Cake\Core\Configure;

foreach ($results as $result) {
    $secureId = Security::encrypt($result->id, Configure::read('SECURITY_KEY'));
    $this->DataTables->prepareData([
        $result['friend']->name,
        $result['friend']->email,
        $result->group,
        $this->Form->postLink(__('Delete'), ['action' => 'delete', $secureId], ['class' => 'btn btn-danger', 'confirm' => __('Are you sure you want to delete # {0}?', $result['friend']->email)])
    ]);
}
echo $this->DataTables->response();
