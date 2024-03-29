<?php

namespace App\Controller;

use App\Controller\AppController;
use DataTables\Controller\DataTablesAjaxRequestTrait;
use Cake\Utility\Hash;

//use Cake\Utility\Security;
//use Cake\Core\Configure;

/**
 * Dashboard Controller
 */
class DashboardController extends AppController {

    use DataTablesAjaxRequestTrait;

    public function initialize() {
        parent::initialize();
        $this->loadComponent('DataTables.DataTables');
        $auth_user_id = $this->request->getSession()->read('Auth.User.id');
        $this->DataTables->createConfig('StreamDetails')
                ->queryOptions([
                    'contain' => ['Streams' => ['Notifications', 'Users']],
                    'conditions' => [
                        'StreamDetails.user_id' => $auth_user_id,
                        'Streams.end_time >= NOW()'
                    ],
                    'group' => 'Streams.id'
                ])
                ->databaseColumn('Users.id')
                ->databaseColumn('Notifications.id')
                ->databaseColumn('StreamDetails.stream_id')
                ->databaseColumn('Streams.is_active')
                ->column('Streams.title', ['label' => 'Title', 'orderable' => false])
                ->column('Streams.start_time', ['label' => 'Start Time'])
                ->column('Streams.end_time', ['label' => 'End Time'])
                ->column('Users.name', ['label' => 'Schedule By', 'orderable' => false])
                ->column('actions', ['label' => 'Actions', 'database' => false]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {
        $this->loadModel('Streams');
        $this->loadModel('Users');
        $stream = $this->Streams->newEntity();
        $user_ids = Hash::extract($this->Auth->user('user_friends'), '{n}.friend_id');
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (!empty($data['emails']) && !empty($user_ids)) {
                $data['start_time'] = $this->dateFormatSql($data['start_time']);
                $data['end_time'] = $this->dateFormatSql($data['end_time']);
                $streamData = $this->Streams->patchEntity($stream, $data);
                if ($this->Streams->save($streamData)) {
                    $this->loadModel('StreamDetails');
                    $streamDetails = ['stream_id' => $streamData->id];
                    $streamDetails['user_id'] = $this->Auth->user('id');
                    $EntityStreamDetails = $this->StreamDetails->newEntity($streamDetails);
                    $this->StreamDetails->save($EntityStreamDetails);
                    foreach ($data['emails'] as $userId) {
                        if (!in_array($userId, $user_ids)) {
                            continue;
                        }
                        $streamDetails['user_id'] = $userId;
                        $EntityStreamDetails = $this->StreamDetails->newEntity($streamDetails);
                        $this->StreamDetails->save($EntityStreamDetails);
                    }
                    $this->Flash->success(__('Your meeting has been scheduled successfully.'));
                    return $this->redirect($this->referer());
                }
                $this->Flash->error(__('Unable to scheduled meeting. Please, try again.'));
            } else {
                $this->Flash->error(__('Please add emails for schedule meeting.'));
            }
        }
        $emails = array();
        if (!empty($user_ids)) {
            foreach ($this->Auth->user('user_friends') as $user_id) {
                $emails[$user_id['group']][$user_id['friend']['id']] = $user_id['friend']['email'];
            }
        }
        $this->set(compact('stream', 'emails'));
        $this->DataTables->setViewVars('StreamDetails');
    }

    public function stream($joinKey) {
        /* TURN SERVER from twilio */
        $proxyauth = 'ACee73bd10554357c23935f9465fe61d88:3e6e17d781c0eaf48ec5ac88be1c9959';
        $ice_server = exec("curl -X POST https://api.twilio.com/2010-04-01/Accounts/ACee73bd10554357c23935f9465fe61d88/Tokens.json -u {$proxyauth}");
        $secureId = base64_decode($joinKey);
        $this->loadModel('StreamDetails');
        $current_user = $this->Auth->user();
        $streamData = $this->StreamDetails->find('all', [
            'conditions' => [
                'StreamDetails.stream_id' => $secureId,
                'StreamDetails.user_id' => $current_user['id'],
                'Streams.end_time >= NOW()'
            ],
            'contain' => ['Streams' => 'Users']
        ]);
        if ($streamData->count() === 0) {
            $this->Flash->error('Meeting not exists.');
            return $this->redirect(['action' => 'index']);
        }
        $stream_data = $streamData->first();
        if ($stream_data['stream']->start_time < date('Y-m-d h:i:s', strtotime("+10 minutes"))) {
            $this->Flash->error('We are unable to start the meeting because meeting starting time is not matching.');
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('mobile')) {
            $mobile_user = '1';
        } else {
            $mobile_user = '0';
        }
        $toggled = true;
        $this->set(compact('stream_data', 'current_user', 'toggled', 'mobile_user', 'ice_server'));
    }

}
