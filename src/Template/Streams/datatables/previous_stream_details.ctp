<?php

//use Cake\Utility\Security;
//use Cake\Core\Configure;

foreach ($results as $result) {
    $this->DataTables->prepareData([
        $result['stream']->title,
        $result['stream']->start_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        $result['stream']->end_time->i18nFormat('MMM dd, yyyy h:mm:ss a'),
        h($result['stream']['user']->name),
    ]);
}
echo $this->DataTables->response();
